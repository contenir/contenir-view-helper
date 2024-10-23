<?php

namespace Contenir\View\Helper;

use Laminas\Form\Element;
use Laminas\Form\View\Helper\FormCollection;
use Laminas\Form\View\Helper\FormElement;
use Laminas\Form\View\Helper\FormElementErrors;
use Laminas\Form\View\Helper\FormLabel;
use Laminas\View\Helper\AbstractHtmlElement;
use ReflectionClass;

use function strip_tags;

class FormGroup extends AbstractHtmlElement
{
    use PHPViewTrait;

    public function __invoke(
        Element $element,
        array $displayAttributes = [],
        array $groupAttributes = [
            'class' => 'form__group'
        ]
    ): string {
        $view = $this->getPHPView();

        if (count($element->getMessages())) {
            $groupAttributes['class'] .= ' error';
        }

        if ($element->getAttribute('group_class')) {
            $groupAttributes['class'] .= ' ' . $element->getAttribute('group_class');
            $element->removeAttribute('group_class');
        }

        if ($element->getAttribute('data-field-dependancy')) {
            $groupAttributes['class'] .= ' form__group--dependancy';
        }

        $groupAttributesString = $view->HtmlAttributes($groupAttributes);

        $reflect     = new ReflectionClass($element);
        $elementType = strtolower($reflect->getShortName());

        if ($elementType == 'collection') {
            return $this
                ->getFormCollection()
                ->setLabelWrapper('<legend class="form__label">%s</legend>')
                ->setElementHelper($view->plugin(FormGroup::class))
                ->render($element);
        }

        if ($element->getAttribute('class') == '') {
            $className = 'form__control';
            switch ($elementType) {
                case 'multi_checkbox':
                case 'checkbox':
                case 'radio':
                case '':
                    $className = '';
                    break;

                case 'fieldset':
                case 'select':
                    $className .= ' form__control--select';
                    break;

            }
            if (count($element->getMessages())) {
                $className .= ' form__control--invalid';
            }
            $element->setAttribute('class', $className);
        }

        if ($element->getAttribute('id') == '') {
            $element->setAttribute('id', sprintf('form-element-%s', $element->getName()));
        }

        $element->setAttributes($displayAttributes);

        $labelAttributes = $element->getLabelAttributes();
        if (! isset($labelAttributes['class'])) {
            $labelAttributes['class'] = 'form__label';
        }

        $element->setLabelAttributes($labelAttributes);

        $labelHtml = ($element->getLabel()) ? $this->getFormLabel()->openTag($element) . $element->getLabel() . $this->getFormLabel()->closeTag(): null;

        $descriptionHtml = '';
        if ($element->getOption('description')) {
            $descriptionContent = $element->getOption('description');
            if (strip_tags($descriptionContent) == $descriptionContent) {
                $descriptionHtml = '<p class="form__description">' . nl2br($view->EscapeHtml($descriptionContent)) . '</p>';
            } else {
                $descriptionHtml = $descriptionContent;
            }
        }

        switch ($elementType) {
            case 'multicheckbox':
            case 'radio':
                $html = '';

                $attributes         = $element->getAttributes();
                $attributes['name'] = $element->getName() . (($elementType == 'multicheckbox') ? '[]' : '');
                $attributes['type'] = ($elementType == 'radio') ? 'radio' : 'checkbox';
                $selectedOptions    = (array)$element->getValue();
                $valueOptions       = (is_callable([$element, 'getValueOptions'])) ? $element->getValueOptions() : [];

                foreach ($valueOptions as $key => $optionSpec) {
                    $value                = '';
                    $label                = '';
                    $inputAttributes      = $attributes;
                    $multiLabelAttributes = [];
                    $selected             = isset($inputAttributes['selected'])
                        && $inputAttributes['type'] !== 'radio'
                        && $inputAttributes['selected'];
                    $disabled             = isset($inputAttributes['disabled']) && $inputAttributes['disabled'];

                    if (is_scalar($optionSpec)) {
                        $optionSpec = [
                            'label' => $optionSpec,
                            'value' => $key,
                        ];
                    }

                    if (isset($optionSpec['value'])) {
                        $value = $optionSpec['value'];
                    }
                    if (isset($optionSpec['label'])) {
                        $label = $optionSpec['label'];
                    }
                    if (isset($optionSpec['selected'])) {
                        $selected = $optionSpec['selected'];
                    }
                    if (isset($optionSpec['disabled'])) {
                        $disabled = $optionSpec['disabled'];
                    }
                    if (isset($optionSpec['label_attributes'])) {
                        $multiLabelAttributes = array_merge($multiLabelAttributes, $optionSpec['label_attributes']);
                    }
                    if (isset($optionSpec['attributes'])) {
                        $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
                    }

                    if (in_array($value, $selectedOptions)) {
                        $selected = true;
                    }

                    $inputAttributes['id']    = $this->getNormalisedId($element->getAttribute('id') . '-' . $value);
                    $inputAttributes['value'] = $value;

                    if ($selected) {
                        $inputAttributes['checked'] = $selected;
                    }

                    if ($disabled) {
                        $inputAttributes['disabled'] = $disabled;
                    }

                    $multiLabelAttributes['for'] = $inputAttributes['id'];

                    $tag        = 'input';
                    $input      = '<' . $tag . $this->htmlAttribs($inputAttributes) . $this->getClosingBracket();

                    $tag        = 'label';
                    $label = '<' . $tag . $this->htmlAttribs($multiLabelAttributes) . '>' . $label . '</' . $tag . '>';

                    $html .= '<div class="form__control--' . $inputAttributes['type'] . '">' . $input . $label . '</div>';
                }

                $html = '<div ' . $groupAttributesString . '>' .
                    $labelHtml .
                    '<div class="form__group--options">' . $html . '</div>' .
                    $this->getFormElementErrors()->render($element, [
                        'class' => 'form__errors'
                    ]) .
                    $descriptionHtml .
                    '</div>';
                break;

            case 'file':
                $html = '<div ' . $groupAttributesString . '>' .
                    $labelHtml .
                    '<span class="form__control--file" data-caption="">' .
                    $this->getFormElement()->render($element) .
                    '</span>' .
                    $this->getFormElementErrors()->render($element, [
                        'class' => 'form__errors'
                    ]) .
                    $descriptionHtml .
                    '</div>';
                break;

            case 'checkbox':
                $html = '<div ' . $groupAttributesString . '>' .
                    '<div class="form__control--checkbox">' .
                    $this->getFormElement()->render($element) .
                    $labelHtml .
                    $this->getFormElementErrors()->render($element, [
                        'class' => 'form__errors'
                    ]) .
                    $descriptionHtml .
                    '</div>' .
                    '</div>';
                break;

            case 'hidden':
                $html = $this->getFormElement()->render($element);
                break;

            case '':
                $html = $labelHtml .
                    $this->getFormElement()->render($element) .
                    $this->getFormElementErrors()->render($element, [
                        'class' => 'form__errors'
                    ]);
                break;

            default:
                $html = '<div ' . $groupAttributesString . '>' .
                    $labelHtml .
                    $this->getFormElement()->render($element) .
                    $this->getFormElementErrors()->render($element, [
                        'class' => 'form__errors'
                    ]) .
                    $descriptionHtml .
                    '</div>';
                break;
        }

        return $html;
    }

    protected function getFormLabel(): FormLabel
    {
        $helper = $this->getPHPView()->plugin(FormLabel::class);
        assert($helper instanceof FormLabel);

        return $helper;
    }

    protected function getFormCollection(): FormCollection
    {
        $helper = $this->getPHPView()->plugin(FormCollection::class);
        assert($helper instanceof FormCollection);

        return $helper;
    }

    protected function getFormElement(): FormElement
    {
        $helper = $this->getPHPView()->plugin(FormElement::class);
        assert($helper instanceof FormElement);

        return $helper;
    }

    protected function getFormElementErrors(): FormElementErrors
    {
        $helper = $this->getPHPView()->plugin(FormElementErrors::class);
        assert($helper instanceof FormElementErrors);

        return $helper;
    }

    protected function getNormalisedId($id): array|string|null
    {
        $id = preg_replace('/[^a-zA-Z0-9_\-]/', '-', strtolower($id));

        return preg_replace('/-{2,}/', '-', $id);
    }
}
