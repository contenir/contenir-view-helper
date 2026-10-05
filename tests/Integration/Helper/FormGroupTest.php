<?php

declare(strict_types=1);

namespace Contenir\View\Tests\Integration\Helper;

use Contenir\View\Helper\FormGroup;
use Contenir\View\Tests\TestAsset\Form\Radio as RadioLike;
use Contenir\View\Tests\Trait\PhpRendererTrait;
use Laminas\Form\Element;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Collection;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FormGroup::class)]
#[Group('integration')]
#[Group('view')]
final class FormGroupTest extends TestCase
{
    use PhpRendererTrait;

    #[Test]
    public function aGroupClassListIsKept(): void
    {
        $element = new Text('q');
        $element->setMessages(['isEmpty' => 'Required']);

        static::assertStringStartsWith(
            '<div  class="form__group&#x20;form__group--wide&#x20;error">',
            $this->render($element, [], ['class' => ['form__group', 'form__group--wide']]),
        );
    }

    #[Test]
    public function aGroupWithoutAClassOnlyGetsTheStateClasses(): void
    {
        $element = new Text('q');
        $element->setMessages(['isEmpty' => 'Required']);

        static::assertStringStartsWith('<div  id="g" class="&#x20;error">', $this->render($element, [], ['id' => 'g']));
    }

    #[Test]
    public function aNonScalarOptionLabelRendersEmpty(): void
    {
        $element = new MultiCheckbox('odd', ['value_options' => [['value' => 'a', 'label' => ['not', 'text']]]]);

        static::assertStringContainsString('<label for="form-element-odd-a"></label>', $this->render($element));
    }

    #[Test]
    public function anOptionDisabledByItsSpecificationOverridesTheElement(): void
    {
        $element = new MultiCheckbox('t', ['value_options' => [['value' => 'a', 'label' => 'A', 'disabled' => true]]]);
        $element->setAttribute('disabled', false);

        static::assertStringContainsString(
            '<input type="checkbox" name="t&#x5B;&#x5D;" disabled="1" class=""',
            $this->render($element),
        );
    }

    #[Test]
    public function anOptionMatchingTheValueIsCheckedEvenWhenItsSpecificationSaysNot(): void
    {
        $element = new MultiCheckbox('t', ['value_options' => [['value' => 'a', 'label' => 'A', 'selected' => false]]]);
        $element->setValue(['a']);

        static::assertStringContainsString('value="a" checked="1"', $this->render($element));
    }

    #[Test]
    public function anOptionStaysDisabledWithTheElementDespiteItsOwnAttributes(): void
    {
        $element = new MultiCheckbox('t', [
            'value_options' => [['value' => 'a', 'label' => 'A', 'attributes' => ['disabled' => false]]],
        ]);
        $element->setAttribute('disabled', true);

        static::assertStringContainsString(
            '<input type="checkbox" name="t&#x5B;&#x5D;" disabled="1" class=""',
            $this->render($element),
        );
    }

    #[Test]
    public function appliesDisplayAndGroupAttributes(): void
    {
        $element = new Text('q', ['label' => 'Search', 'label_attributes' => ['class' => 'sr-only']]);

        static::assertSame(
            '<div  class="search" data-role="search"><label class="sr-only" for="form-element-q">Search</label>'
                . '<input type="text" name="q" class="form__control" id="form-element-q" placeholder="Search" value=""></div>',
            $this->render($element, ['placeholder' => 'Search'], ['class' => 'search', 'data-role' => 'search']),
        );
    }

    #[Test]
    public function aRadioLikeElementWithoutValueOptionsRendersNoOptions(): void
    {
        static::assertSame(
            '<div  class="form__group"><div class="form__group--options"></div></div>',
            $this->render(new RadioLike('choice')),
        );
    }

    #[Test]
    public function escapesPlainTextDescriptionsAndKeepsTheirLineBreaks(): void
    {
        $element = new Text('bio', ['description' => "Tell us about you & yours\nBriefly"]);

        static::assertSame(
            '<div  class="form__group"><input type="text" name="bio" class="form__control" id="form-element-bio" value="">'
                . "<p class=\"form__description\">Tell us about you &amp; yours<br />\nBriefly</p></div>",
            $this->render($element),
        );
    }

    #[Test]
    public function givesFieldsetsTheSelectControlClass(): void
    {
        $element = new Fieldset('address');
        $this->render($element);

        static::assertSame('form__control form__control--select', $element->getAttribute('class'));
    }

    #[Test]
    public function givesSelectsTheSelectControlClass(): void
    {
        $element = new Select('size', ['value_options' => ['s' => 'Small']]);

        static::assertSame(
            '<div  class="form__group"><select name="size" class="form__control&#x20;form__control--select" '
                . 'id="form-element-size"><option value="s">Small</option></select></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function inheritsSelectedAndDisabledFromTheElement(): void
    {
        $element = new MultiCheckbox('all', ['value_options' => ['a' => 'A']]);
        $element->setAttributes(['selected' => true, 'disabled' => true]);

        static::assertSame(
            '<div  class="form__group"><div class="form__group--options"><div class="form__control--checkbox">'
                . '<input type="checkbox" name="all&#x5B;&#x5D;" selected="1" disabled="1" class="" id="form-element-all-a" '
                . 'value="a" checked="1"><label for="form-element-all-a">A</label></div></div></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function listsErrorsBeforeTheDescription(): void
    {
        $element = new Text('email', ['description' => 'Your work address']);
        $element->setMessages(['isEmpty' => 'Required']);

        static::assertStringEndsWith(
            '<ul class="form__errors"><li>Required</li></ul><p class="form__description">Your work address</p></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function marksInvalidFieldsAndListsTheirErrors(): void
    {
        $element = new Text('email');
        $element->setMessages(['isEmpty' => 'Required']);

        static::assertSame(
            '<div  class="form__group&#x20;error"><input type="text" name="email" '
                . 'class="form__control&#x20;form__control--invalid" id="form-element-email" value="">'
                . '<ul class="form__errors"><li>Required</li></ul></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function movesTheGroupClassAndFlagsDependentFields(): void
    {
        $element = new Text('city');
        $element->setAttributes(['group_class' => 'half', 'data-field-dependancy' => 'country', 'class' => 'own']);

        static::assertSame(
            '<div  class="form__group&#x20;half&#x20;form__group--dependancy"><input type="text" name="city" '
                . 'data-field-dependancy="country" class="own" id="form-element-city" value=""></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function nonScalarValuesNeverSelectAnOption(): void
    {
        $element = new MultiCheckbox('tags', ['value_options' => ['' => 'Blank']]);
        $element->setValue([['nested']]);

        static::assertStringNotContainsString('checked', $this->render($element));
    }

    #[Test]
    public function optionsWithoutAValueOrLabelRenderEmpty(): void
    {
        $element = new MultiCheckbox('partial', ['value_options' => [['label' => 'No value'], ['value' => 'x']]]);

        static::assertSame(
            '<div  class="form__group"><div class="form__group--options">'
                . '<div class="form__control--checkbox"><input type="checkbox" name="partial&#x5B;&#x5D;" class="" '
                . 'id="form-element-partial-" value=""><label for="form-element-partial-">No value</label></div>'
                . '<div class="form__control--checkbox"><input type="checkbox" name="partial&#x5B;&#x5D;" class="" '
                . 'id="form-element-partial-x" value="x"><label for="form-element-partial-x"></label></div>'
                . '</div></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function outputsHtmlDescriptionsAsTheyAre(): void
    {
        $element = new Text('bio', ['description' => '<em>Optional</em>']);
        $element->setAttribute('id', 'bio');

        static::assertSame(
            '<div  class="form__group"><input type="text" name="bio" id="bio" class="form__control" value="">'
                . '<em>Optional</em></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function radiosIgnoreAnElementWideSelection(): void
    {
        $element = new Radio('one', ['value_options' => ['a' => 'A']]);
        $element->setAttribute('selected', true);

        static::assertStringNotContainsString('checked', $this->render($element));
    }

    #[Test]
    public function removesTheGroupClassFromTheElement(): void
    {
        $element = new Text('city');
        $element->setAttribute('group_class', 'half');
        $this->render($element);

        static::assertFalse($element->hasAttribute('group_class'));
    }

    #[Test]
    public function rendersATextFieldWithItsLabel(): void
    {
        $element = new Text('name', ['label' => 'Name']);

        static::assertSame(
            '<div  class="form__group"><label class="form__label" for="form-element-name">Name</label>'
                . '<input type="text" name="name" class="form__control" id="form-element-name" value=""></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function rendersCollectionsAsLegendedGroupsOfFormGroups(): void
    {
        $element = new Collection('people', [
            'label'          => 'People',
            'count'          => 1,
            'target_element' => new Text('name'),
        ]);
        $element->prepareElement(new Form());

        static::assertSame(
            '<fieldset name="people"><legend class="form__label">People</legend>'
                . '<div  class="form__group"><input type="text" name="people&#x5B;0&#x5D;" class="form__control" '
                . 'id="form-element-people&#x5B;0&#x5D;" value=""></div>'
                . '</fieldset>',
            $this->render($element),
        );
    }

    #[Test]
    public function rendersHiddenInputsBare(): void
    {
        $element = new Hidden('token');
        $element->setValue('abc');

        static::assertSame(
            '<input type="hidden" name="token" class="form__control" id="form-element-token" value="abc">',
            $this->render($element),
        );
    }

    #[Test]
    public function rendersMultiCheckboxOptionsFromFullSpecifications(): void
    {
        $element = new MultiCheckbox('topics', [
            'value_options' => [
                ['value' => 'php', 'label' => 'PHP', 'label_attributes' => ['class' => 'chip']],
                ['value' => 'js', 'label' => 'JS', 'selected' => true],
                ['value' => 'go', 'label' => 'Go', 'disabled' => true, 'attributes' => ['data-x' => 'y']],
                ['value' => 'rs', 'label' => 'Rust'],
            ],
        ]);
        $element->setAttribute('id', 'Topics List');
        $element->setValue(['rs']);

        static::assertSame(
            '<div  class="form__group"><div class="form__group--options">'
                . '<div class="form__control--checkbox"><input type="checkbox" name="topics&#x5B;&#x5D;" id="topics-list-php" class="" value="php">'
                . '<label class="chip" for="topics-list-php">PHP</label></div>'
                . '<div class="form__control--checkbox"><input type="checkbox" name="topics&#x5B;&#x5D;" id="topics-list-js" class="" value="js" checked="1">'
                . '<label for="topics-list-js">JS</label></div>'
                . '<div class="form__control--checkbox"><input type="checkbox" name="topics&#x5B;&#x5D;" id="topics-list-go" class="" data-x="y" value="go" disabled="1">'
                . '<label for="topics-list-go">Go</label></div>'
                . '<div class="form__control--checkbox"><input type="checkbox" name="topics&#x5B;&#x5D;" id="topics-list-rs" class="" value="rs" checked="1">'
                . '<label for="topics-list-rs">Rust</label></div>'
                . '</div></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function rendersRadioOptionsWithTheCurrentValueChecked(): void
    {
        $element = new Radio('colour', ['label' => 'Colour', 'value_options' => ['red' => 'Red', 'blue' => 'Blue']]);
        $element->setValue('blue');

        static::assertSame(
            '<div  class="form__group"><label class="form__label" for="form-element-colour">Colour</label>'
                . '<div class="form__group--options">'
                . '<div class="form__control--radio"><input type="radio" name="colour" class="" id="form-element-colour-red" value="red">'
                . '<label for="form-element-colour-red">Red</label></div>'
                . '<div class="form__control--radio"><input type="radio" name="colour" class="" id="form-element-colour-blue" value="blue" checked="1">'
                . '<label for="form-element-colour-blue">Blue</label></div>'
                . '</div></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function wrapsCheckboxesWithTheLabelAfterTheControl(): void
    {
        $element = new Checkbox('agree', ['label' => 'I agree', 'use_hidden_element' => false]);

        static::assertSame(
            '<div  class="form__group"><div class="form__control--checkbox">'
                . '<input type="checkbox" name="agree" class="" id="form-element-agree" value="1">'
                . '<label class="form__label" for="form-element-agree">I agree</label></div></div>',
            $this->render($element),
        );
    }

    #[Test]
    public function wrapsFileInputsForStyling(): void
    {
        $element = new File('upload');

        static::assertSame(
            '<div  class="form__group"><span class="form__control--file" data-caption="">'
                . '<input type="file" name="upload" class="form__control" id="form-element-upload"></span></div>',
            $this->render($element),
        );
    }

    /**
     * @param array<string, scalar|null>                         $displayAttributes
     * @param array<string, scalar|array<array-key, mixed>|null> $groupAttributes
     */
    private function render(Element $element, array $displayAttributes = [], ?array $groupAttributes = null): string
    {
        $helper = $this->createRenderer()->plugin(FormGroup::class);

        return null === $groupAttributes
            ? $helper($element, $displayAttributes)
            : $helper($element, $displayAttributes, $groupAttributes);
    }
}
