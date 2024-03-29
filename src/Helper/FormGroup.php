<?php

namespace Contenir\View\Helper;

use Laminas\Form\Element;
use Laminas\View\Helper\AbstractHtmlElement;
use ReflectionClass;

class FormGroup extends AbstractHtmlElement
{
    public function __invoke(
        Element $element,
        array $displayAttributes = [],
        array $groupAttributes = [
            'class' => 'form__group'
        ]
    ) {
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

        $groupAttributesString = $this->view->HtmlAttributes($groupAttributes);

        $reflect     = new ReflectionClass($element);
        $elementType = strtolower($reflect->getShortName());

        if ($elementType == 'collection') {
            $html = $this
                ->view
                ->formCollection()
                ->setLabelWrapper('<legend class="form__label">%s</legend>')
                ->setElementHelper($this->view->plugin(FormGroup::class))
                ->render($element);
            return $html;
        }

        if ($element->getAttribute('class') == '') {
            $className = 'form__control';
            switch ($elementType) {
                case 'multi_checkbox':
                case 'checkbox':
                case '':
                    $className = '';
                    break;

                case 'radio':
                    $className = '';
                    break;

                case 'select':
                    $className .= ' form__control--select';
                    break;

                case 'fieldset':
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

        $labelHtml = ($element->getLabel()) ? nl2br($this->view->formLabel($element)) : null;

        $descriptionHtml = '';
        if ($element->getOption('description')) {
            $descriptionContent = $element->getOption('description');
            if (\strip_tags($descriptionContent) == $descriptionContent) {
                $descriptionHtml = '<p class="form__description">' . nl2br($this->view->EscapeHtml($descriptionContent)) . '</p>';
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
                $selectedOptions    = (array) $element->getValue();
                $count              = 0;

                foreach ($element->getValueOptions() as $key => $optionSpec) {
                    $value                = '';
                    $label                = '';
                    $inputAttributes      = $attributes;
                    $multiLabelAttributes = [];
                    $selected             = isset($inputAttributes['selected'])
                                                                    && $inputAttributes['type'] !== 'radio'
                                                                    && $inputAttributes['selected'];
                    $disabled = isset($inputAttributes['disabled']) && $inputAttributes['disabled'];

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
                        $multiLabelAttributes = isset($multiLabelAttributes)
                            ? array_merge($multiLabelAttributes, $optionSpec['label_attributes'])
                            : $optionSpec['label_attributes'];
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

                    $input = sprintf(
                        '<input %s>',
                        $this->htmlAttribs($inputAttributes),
                    );

                    $label = sprintf(
                        '<label%s>%s</label>',
                        $this->htmlAttribs($multiLabelAttributes),
                        $label
                    );

                    $html .= '<div class="form__control--' . $attributes['type'] . '">' . $input . $label . '</div>';
                };

                $html = '<div ' . $groupAttributesString . '>' .
                        $labelHtml .
                        '<div class="form__group--options">' . $html . '</div>' .
                        $this->view->formElementErrors($element, [
                            'class' => 'form__errors'
                        ]) .
                        $descriptionHtml .
                        '</div>';
                break;

            case 'file':
                $html = '<div ' . $groupAttributesString . '>' .
                        $labelHtml .
                        '<span class="form__control--file" data-caption="">' .
                        $this->view->formElement($element) .
                        '</span>' .
                        $this->view->formElementErrors($element, [
                            'class' => 'form__errors'
                        ]) .
                        $descriptionHtml .
                        '</div>';
                break;

            case 'checkbox':
                $html = '<div ' . $groupAttributesString . '>' .
                        '<div class="form__control--checkbox">' .
                        $this->view->formElement($element) .
                        $labelHtml .
                        $this->view->formElementErrors($element, [
                            'class' => 'form__errors'
                        ]) .
                        $descriptionHtml .
                        '</div>' .
                        '</div>';
                break;

            case 'hidden':
                $html = $this->view->formElement($element);
                break;

            case '':
                $html = $labelHtml .
                        $this->view->formElement($element) .
                        $this->view->formElementErrors($element, [
                            'class' => 'form__errors'
                        ]);
                break;

            default:
                $html = '<div ' . $groupAttributesString . '>' .
                        $labelHtml .
                        $this->view->formElement($element) .
                        $this->view->formElementErrors($element, [
                            'class' => 'form__errors'
                        ]) .
                        $descriptionHtml .
                        '</div>';
                break;
        }

        return $html;
    }

    public function getNormalisedId($id)
    {
        $id = preg_replace('/[^a-zA-Z0-9_\-]/', '-', strtolower($id));
        $id = preg_replace('/[\-]{2,}/', '-', $id);
        return $id;
    }
}
