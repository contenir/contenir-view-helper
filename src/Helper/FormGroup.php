<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Exception\ExceptionInterface as FormException;
use Laminas\Form\View\Helper\FormCollection;
use Laminas\Form\View\Helper\FormElement;
use Laminas\Form\View\Helper\FormElementErrors;
use Laminas\Form\View\Helper\FormLabel;
use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\AbstractHtmlElement;
use Laminas\View\Helper\HtmlAttributes;

use function array_filter;
use function array_map;
use function array_values;
use function basename;
use function implode;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function nl2br;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strip_tags;
use function strtolower;

/**
 * Renders a laminas-form element as a BEM "form__group": label, control,
 * errors and description, with option lists for radios and multi-checkboxes
 * and a <legend>-labelled group for collections.
 *
 * Element options and attributes it reads: the "description" option (plain
 * text is escaped and line breaks kept; HTML is output as is), the
 * "group_class" attribute (added to the group and removed from the
 * element) and "data-field-dependancy" (adds form__group--dependancy).
 *
 * @api
 */
final class FormGroup extends AbstractHtmlElement
{
    use PHPViewTrait;

    private const array ERROR_ATTRIBUTES = ['class' => 'form__errors'];

    private static function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * Gives an element without a class the BEM control class for its type.
     */
    private function applyControlClass(Element $element, string $elementType, bool $hasErrors): void
    {
        if (! in_array($element->getAttribute('class'), [null, ''], strict: true)) {
            return;
        }

        $className = match ($elementType) {
            'multicheckbox', 'checkbox', 'radio' => '',
            'fieldset', 'select'                 => 'form__control form__control--select',
            default                              => 'form__control',
        };

        if ($hasErrors) {
            $className .= ' form__control--invalid';
        }

        $element->setAttribute('class', $className);
    }

    private function descriptionHtml(mixed $description): string
    {
        if (! is_string($description) || '' === $description) {
            return '';
        }

        if (strip_tags($description) !== $description) {
            return $description;
        }

        return '<p class="form__description">' . nl2br($this->escapeHtml($description)) . '</p>';
    }

    /**
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    private function getFormCollection(): FormCollection
    {
        return $this->getPHPView()->plugin(FormCollection::class);
    }

    /**
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    private function getFormElement(): FormElement
    {
        return $this->getPHPView()->plugin(FormElement::class);
    }

    /**
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    private function getFormElementErrors(): FormElementErrors
    {
        return $this->getPHPView()->plugin(FormElementErrors::class);
    }

    /**
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    private function getFormLabel(): FormLabel
    {
        return $this->getPHPView()->plugin(FormLabel::class);
    }

    private function getNormalisedId(string $id): string
    {
        $id = (string) preg_replace('/[^a-zA-Z0-9_\-]/', replacement: '-', subject: strtolower($id));

        return (string) preg_replace('/-{2,}/', replacement: '-', subject: $id);
    }

    /**
     * @param mixed $class The group's class attribute: a string, or a list of classes.
     */
    private function groupClass(Element $element, mixed $class, bool $hasErrors): string
    {
        $class = is_array($class)
            ? implode(' ', array_map(self::scalarString(...), $class))
            : self::scalarString($class);

        if ($hasErrors) {
            $class .= ' error';
        }

        $groupClass = $element->getAttribute('group_class');
        if (is_string($groupClass) && '' !== $groupClass) {
            $class .= " {$groupClass}";
            $element->removeAttribute('group_class');
        }

        if ((bool) $element->getAttribute('data-field-dependancy')) {
            $class .= ' form__group--dependancy';
        }

        return $class;
    }

    /**
     * Renders one value option of a radio or multi-checkbox element.
     *
     * @param array{type: string, id: string, ...<string, mixed>} $attributes     The element's attributes, shared by every
     *                                                                option.
     * @param mixed                                   $optionSpec     A label, or an array of value, label,
     *                                                                selected, disabled, label_attributes and
     *                                                                attributes.
     * @param list<string>                            $selectedValues The element's current values.
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     *
     * @mago-expect analysis:mixed-operand Option flags are loose form configuration; any truthy value counts.
     */
    private function renderOption(array $attributes, mixed $optionSpec, int|string $key, array $selectedValues): string
    {
        $spec  = is_array($optionSpec) ? $optionSpec : ['label' => $optionSpec, 'value' => $key];
        $value = self::scalarString($spec['value'] ?? '');

        $selected = 'radio' !== $attributes['type'] && (bool) ($attributes['selected'] ?? false);
        $selected = (bool) ($spec['selected'] ?? $selected) || in_array($value, $selectedValues, strict: true);
        $disabled = (bool) ($spec['disabled'] ?? $attributes['disabled'] ?? false);

        $inputAttributes = [
            ...$attributes,
            ...(is_array($spec['attributes'] ?? null) ? $spec['attributes'] : []),
            'id'    => $this->getNormalisedId("{$attributes['id']}-{$value}"),
            'value' => $value,
        ];

        if ($selected) {
            $inputAttributes['checked'] = true;
        }

        if ($disabled) {
            $inputAttributes['disabled'] = true;
        }

        $labelAttributes = [
            ...(is_array($spec['label_attributes'] ?? null) ? $spec['label_attributes'] : []),
            'for' => $inputAttributes['id'],
        ];

        return sprintf(
            '<div class="form__control--%s"><input%s%s<label%s>%s</label></div>',
            $attributes['type'],
            $this->htmlAttribs($inputAttributes),
            $this->getClosingBracket(),
            $this->htmlAttribs($labelAttributes),
            self::scalarString($spec['label'] ?? ''),
        );
    }

    /**
     * Renders one <input> and <label> per value option of a radio or
     * multi-checkbox element.
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     *
     * @mago-expect analysis:invalid-type-cast An element value is a scalar, a list, or null for none.
     */
    private function renderOptions(Element $element, string $elementType): string
    {
        $attributes = [
            ...$element->getAttributes(),
            'name' => (string) $element->getName() . ('multicheckbox' === $elementType ? '[]' : ''),
            'type' => 'radio' === $elementType ? 'radio' : 'checkbox',
            'id'   => self::scalarString($element->getAttribute('id')),
        ];

        $selectedValues = array_map(
            self::scalarString(...),
            array_values(array_filter((array) $element->getValue(), is_scalar(...))),
        );
        $valueOptions = $element instanceof MultiCheckbox ? $element->getValueOptions() : [];

        $html = [];
        foreach ($valueOptions as $key => $optionSpec) {
            $html[] = $this->renderOption($attributes, $optionSpec, $key, $selectedValues);
        }

        return implode('', $html);
    }

    /**
     * @param array<string, scalar|null>                         $displayAttributes Attributes set on the element
     *                                                                              before rendering.
     * @param array<string, scalar|array<array-key, mixed>|null> $groupAttributes   Attributes of the wrapping
     *                                                                              group <div>.
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     * @throws FormException when laminas-form cannot render the element.
     */
    public function __invoke(
        Element $element,
        array $displayAttributes = [],
        array $groupAttributes = ['class' => 'form__group'],
    ): string {
        $view        = $this->getPHPView();
        $hasErrors   = [] !== $element->getMessages();
        $elementType = strtolower(basename(str_replace(
            search: '\\',
            replace: '/',
            subject: $element::class,
        )));

        $groupAttributes['class'] = $this->groupClass($element, $groupAttributes['class'] ?? '', $hasErrors);
        $groupOpen                = sprintf(
            '<div %s>',
            $view->plugin(HtmlAttributes::class)($groupAttributes)->__toString(),
        );

        if ('collection' === $elementType) {
            return $this->getFormCollection()
                ->setLabelWrapper('<legend class="form__label">%s</legend>')
                ->setElementHelper($view->plugin(self::class))
                ->render($element);
        }

        $this->applyControlClass($element, $elementType, $hasErrors);

        if (in_array($element->getAttribute('id'), [null, ''], strict: true)) {
            $element->setAttribute('id', sprintf('form-element-%s', (string) $element->getName()));
        }

        $element->setAttributes($displayAttributes);

        $labelAttributes          = $element->getLabelAttributes();
        $labelAttributes['class'] ??= 'form__label';
        $element->setLabelAttributes($labelAttributes);

        $label     = (string) $element->getLabel();
        $labelHtml = '' === $label
            ? ''
            : $this->getFormLabel()->openTag($element) . $label . $this->getFormLabel()->closeTag();

        $trailer =
            $this->getFormElementErrors()->render($element, self::ERROR_ATTRIBUTES)
            . $this->descriptionHtml($element->getOption('description'));

        return match ($elementType) {
            'multicheckbox', 'radio' => sprintf(
                '%s%s<div class="form__group--options">%s</div>%s</div>',
                $groupOpen,
                $labelHtml,
                $this->renderOptions($element, $elementType),
                $trailer,
            ),
            'file' => sprintf(
                '%s%s<span class="form__control--file" data-caption="">%s</span>%s</div>',
                $groupOpen,
                $labelHtml,
                $this->getFormElement()->render($element),
                $trailer,
            ),
            'checkbox' => sprintf(
                '%s<div class="form__control--checkbox">%s%s%s</div></div>',
                $groupOpen,
                $this->getFormElement()->render($element),
                $labelHtml,
                $trailer,
            ),
            'hidden'                 => $this->getFormElement()->render($element),
            default                  => sprintf(
                '%s%s%s%s</div>',
                $groupOpen,
                $labelHtml,
                $this->getFormElement()->render($element),
                $trailer,
            ),
        };
    }
}
