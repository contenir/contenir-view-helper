# Form groups

`formGroup(Element $element, array $displayAttributes = [], array $groupAttributes = ['class' => 'form__group']): string`
renders a laminas-form element as BEM markup:

```html
<div  class="form__group">
    <label class="form__label" for="form-element-name">Name</label>
    <input type="text" name="name" class="form__control" id="form-element-name" value="">
    <ul class="form__errors">…</ul>
    <p class="form__description">…</p>
</div>
```

It needs `laminas/laminas-i18n`, like the laminas-form view helpers it
renders through.

## Classes and ids

- An element without a class gets `form__control`; selects (and fieldsets)
  `form__control form__control--select`; checkboxes, radios and
  multi-checkboxes none. Invalid elements add `form__control--invalid`.
- An element without an id gets `form-element-<name>`.
- Invalid elements add `error` to the group class.
- The element's `group_class` attribute is moved onto the group.
- A `data-field-dependancy` attribute adds `form__group--dependancy` to the
  group.
- Labels default to `class="form__label"`.

## Element types

| Type | Markup |
| --- | --- |
| Radio, MultiCheckbox | Label, then `<div class="form__group--options">` with one `<div class="form__control--radio|checkbox"><input><label></div>` per value option |
| Checkbox | `<div class="form__control--checkbox">` holding the control, then the label |
| File | The control wrapped in `<span class="form__control--file" data-caption="">` |
| Hidden | The bare `<input>` |
| Collection | laminas-form's `formCollection()` with a `<legend class="form__label">`, rendering each child with `formGroup()` |
| Anything else | Label, control |

Option ids are the element id plus the option value, lower-cased with
other characters replaced by `-`. Options are checked when the element's
value contains them, or when the option (or, for multi-checkboxes, the
element) is marked `selected`; `disabled` works the same way.

## Description

The `description` option is rendered after the errors. Plain text is
escaped and keeps its line breaks in `<p class="form__description">`;
text containing HTML tags is output as it is.

## Arguments

- `$displayAttributes` are set on the element before rendering.
- `$groupAttributes` are the group `<div>`'s attributes. Its `class` may be
  a string or a list of classes.
