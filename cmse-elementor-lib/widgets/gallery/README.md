# XML Tags Structure
```xml
<?xml version="1.0" encoding="utf-8"?>
<widget icon="" cat="">
	<fieldset id="" tab="" note="" label="">
		<field type="" name="" label="" />
		<field type="repeat" name="" titlefield="">
			<repeat type="" />
		 </field>
	</fieldset>
</widget>
```

# XML Attributes
**Impotant** where values must be passed as array to Elementor, the method is in JSON object format and **must** use **single quotes** as the delimiter, the obvious reason being that double quotes are used for the XML attributes and there will be conflict and error.  eg: `condition="{'the-keey': 'The Value'}"`

### selectors
The value is passed to Elementor's preview update to apply the CSS changes as made in real time.
**Note** while Elementor uses 2 different indices `selector` and `selectors` to define the method of execution, only the pluralized **selectors** is used for this markup process for either state. The interpreter will automatically decide which index to apply based on the value passed.

**Example** to pass an element class names only that would use Elementor's singular term `selector` because no colon character is detected and therefore not interpreted as array. Note that no open or closing JSON object brackets are used.
```xml
<field selectors="{{WRAPPER}} .myclass" />
```
**Example** to pass an array that woud use Elementor's plural term `selectors` to pass element class names, CSS property and value. Note the open and closing JSON object brackets used along with **single quote** delimiters and **:** colon. All characters within single quotes will be treated as string
```xml
<field selectors="{'{{WRAPPER}} .myclass': 'color: {{VALUE}}'}" />
```
Output as 
```php
Array
(
    [{{WRAPPER}} .myclass] => color: {{VALUE}}
)
```
### type
Set the control type
```xml
<field type="text" />
```
### name
Set the field name. Also used for the control's parent wrapper element class append `elementor-control-myfieldname`. All field names **must be unique**.
```xml
<field name="myfieldname" />
```
### label
The label displayed on the page
```xml
<field label="The Show Host" />
```
### labelblock
A method to align control containers vertically. Most elements align horizontally by default with the label on the left and field on right. This only uses boolean true|false, similar to 1|0
```xml
<field labelblock="1" />
```
### default
Set the default value for a field
```xml
<field default="Host Mike Palmer" />
```
### condition
Set a condition for a specific to be in view if the value of a corresponding field or array of fields match the set values
```xml
<field condition="{'fieldname-to-check': 'value-match','other-field-check': '!value-not-match'}" />
```
### options
Used in association with types of **list** and **list2** and **radio**
```xml
<field options="{'opt-value': 'The first choice','opt-value2': 'The other choice'}" />
```
An **associative** array from a set variable, defined constant, public function, class method can be used. Methods:
```php
public function funcName($argument) {
  return getCategories($argument);
}
```
```xml
<field options="{'funcName': 'posts,products'}" />
```
From a class using **static** method
```xml
<field options="{'cmse::funcName': 'posts,products'}" />
```
If an **associative** array is stored in a variable, and **available in the widget's pilot file** where method name is `_register_controls()`, the index **must** be keyword **var** and the value be the name of the variable without **$**
```php
$varname = ['choice'=>'The text','choice2'=>'More text'];
```
```xml
<field options="{'var': 'varname'}" />
```
### onchange
Used in association with type **list** to execute an action on another field when a change of option is triggered, and update the preview window on certain commands. Using jQuery methods. Available options **val** `val()` | **alert** `css("background","yellow")` not the JS alert dialogue, just to highlight a field that must be used in association with the selection.

The following format is set to pass the value of selected option to the field named `layout` when a specific option value is selected. The last argument is optional. When not specified, any option selected will trigger the action.
```xml
<field type="list" onchange="layout,val,on-this-option-val" />
```
### gtype
Used in asso,ciation with type **controlgroup** to define the group fields which will be used: background | border | typography | box_shadow | text_shadow
```xml
<field type="controlgroup" gtype="border" />
```
### gtypes
This attribute delivers the variants of certain group controls. Maybe it should be called variants, but gtypes was chosen to assert association and lessen confusion.
```xml
<field type="controlgroup" gtype="bg" gtypes="classic,gradient,video" />
```
### min and max
Used in association with type **num** a number type
```xml
<field type="num" min="0" max="50" />
```
### rows
Used in associtiaon with type **textarea** to define height by horizontal row count
```xml
<field type="textarea" rows="10" />
```
### note
Used to write a plain text description below the control. This can include HTML written as html encoded entity eg: `&lt;strong&gt;The word&lt;/strong&gt;` This can be quite tedious and confusion so use is purely user choice.
```xml
<field note="This field is for field struff" />
```
### hint
Used to insert placeholder field attribute for types of text, textarea
```xml
<field type="text" hint="eg: https://googly.com" />
```
### title
Used only with type **hr** to insert `<h5>` title within the separator line
```xml
<field type="hr" title="Tab Colors" />
```
![Separator Image](https://websitedons.net/assets/septitle.jpg)




## Fieldset Attributes
The `<fieldset>` is required to create the accordion panels and create a wrapper around controls. It only uses the 4 listed below
### id, tab, note, label

* The **id** is obviously required to create a **unique** identity for the panel
* The **tab** is required to determine what group the fieldset will be inserted options are tab | tabstyle | tabadv
* The **note** is optional to state a descriptive message immediately below the panel heading when opened
* The **label** is required to label the panel head
```xml
<fieldset id="thegroup" tab="tab" note="A message that displays below the panel tab" label="User Stuff">
    <field />
</fieldset>
```
![Fieldset Note Image](https://websitedons.net/assets/fsetnote.jpg)

## Widget Attributes
The `<widget>` tag attributes creates the widget element in the widgets list
### icon, cat

* The **icon** is to visually clarify the widget's purpose. A default is set in the constants. ![Get Elementor icons](https://elementor.github.io/elementor-icons/)
* The **cat** is **required** to determine the widget group where the widget will be listed. Available Elementor core groups are basic | pro-elements | general | wordpress | woocommerce-elements

Your custom category **must** be set via constants file `constants.php` where set names are: PANELID and PANELCAT and PANELICON
```php
// widgets panel section ID, icon and name
'panelid'		=>	'my-widgetcat',
'panelcat'		=>	'My Great Widgets',
'panelicon'		=>	'eicon-apps', // a default icon if none is set in xml
```

```xml
<widget icon="eicon-play-o" cat="my-wdgets-group">
    <fieldset>
        <field />
    </fieldset>
</widget>
```
![Custom Widget Category Image](https://websitedons.net/assets/customcat.jpg)

# Field Types (controls)

Definitions of the available field types, known as controls in the Elementor community.

### text
A default text input field that accepts attributes `hint` and `default`

Output as
```html
<input id="elementor-control-default-c1356" type="text" class="tooltip-target elementor-control-tag-area" data-tooltip="" data-setting="streamurl" placeholder="https://streaming.radio.co/sf7fa724c2/listen" original-title="">
```
### textarea
A default textarea input

Output as
```html
<textarea id="elementor-control-default-c1383" class="elementor-control-tag-area" rows="10" data-setting="layout" placeholder="">default value</textarea>
```
### list
A standard option select field

Output as
```html
<select id="elementor-control-default-c1382" data-setting="layoutpreset">
<option value="aplayer">Audio Player</option>
<option value="foot">Fixed Foot Player</option>
<option value="schedule">Schedule Table</option>
</select>
```
### list2
A select2 searchable jQuery plugin option select field with search

Output as
```html
<div class="elementor-control-input-wrapper elementor-control-unit-5">
	<select id="elementor-control-default-c1921" class="elementor-select2 select2-hidden-accessible" type="select2" data-setting="svgbg" data-select2-id="elementor-control-default-c1921" tabindex="-1" aria-hidden="true">
		<option value="angle2" data-select2-id="47">angle2</option>
		<option value="angle4" data-select2-id="48">angle4</option>
		<option value="angular" data-select2-id="49">angular</option>
	</select>
    <span class="select2 select2-container select2-container--default select2-container--below select2-container--focus" dir="ltr" data-select2-id="44" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-elementor-control-default-c1921-container"><span class="select2-selection__rendered" id="select2-elementor-control-default-c1921-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder"></span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span>
	</span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
</div>
```
![Select2 Image](https://websitedons.net/assets/select2.jpg)

### num
Standard number input field type

Output as
```html
<input id="elementor-control-default-c1868" type="number" min="0" max="200" step="" class="tooltip-target elementor-control-tag-area elementor-control-unit-2" data-tooltip="" data-setting="btnsize" placeholder="" original-title="">
```

### date
A dynamic date picker using vendor plugin Flatpickr

Output as
```html
<input id="elementor-control-default-c2329" placeholder="08/16/2020" class="elementor-date-time-picker flatpickr flatpickr-input active" type="text" data-setting="endtime" readonly="readonly">
```
![Date picker Image](https://websitedons.net/assets/datepick.jpg)

### hr
Insert a horizontal line for visual separation. This type only uses the attribute `type` along with optional `title`. Its unique ID is auto generated by an incremental counter.
```xml
<field type="hr" />
```

Output as
```html
<!-- as separator only -->
<div class="elementor-control elementor-control-hr213 elementor-control-type-raw_html elementor-label-inline elementor-control-separator-default">
	<div class="elementor-control-content">
		<div class="elementor-control-raw-html ">
			<hr>
		</div>
	</div>
</div>
<!-- with title attribute set -->
<div class="elementor-control elementor-control-hr213 elementor-control-type-raw_html elementor-label-inline elementor-control-separator-default">
	<div class="elementor-control-content">
		<div class="elementor-control-raw-html ">
			<div class="hrblock">
				<h5>
					<span><hr></span>
					<span>Head</span>
					<span><hr></span>
				</h5>
			</div>
		</div>
	</div>
</div>
```

### color
Set a color picker with ability to set transparency. Uses jQuery plugin

Output as
```html
<div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper elementor-control-unit-5"><span class="elementor-control-spinner" style="display: none;"><i class="eicon-spinner eicon-animation-spin"></i></span>
	<div class="e-global__popover-toggle elementor-control-unit-1"><i class="eicon-globe" original-title=""></i></div>
	<div class="pickr elementor-control-unit-1 elementor-control-tag-area">
		<button type="button" class="pcr-button" role="button" aria-label="toggle color picker dialog" original-title="" style="color: rgb(206, 10, 10);"></button>
	</div>
</div>
```
![Color picker Image](https://websitedons.net/assets/colorpick.jpg)

### radio
This displays as an on/off switch as a `<input type="checkbox" />` would, but called radio because it started as such as was just grand-fathered in. This defaults to Yes / No label and value 1 / 0. Those defaults can be changed using `returnval="yes"` `labelon="Set On"` `labeloff="Set Off"`
```xml
<field type="radio" name="aswitch" default="1" label="Do you Want?" />
```

Output as
```html
<label class="elementor-switch elementor-control-unit-2">
	<input id="elementor-control-default-c3413" type="checkbox" data-setting="controls" class="elementor-switch-input" value="1">
	<span class="elementor-switch-label" data-on="Yes" data-off="No"></span>
	<span class="elementor-switch-handle"></span>
</label>
```
![On Off Switch Image](https://websitedons.net/assets/radio.jpg)

### rich
WYSIWYG editor

### img
image/media selector

### hidden
standard hidden field

### url
elemetor ulr function

### choose
elementor choose switch

### gal
elementor gallery

### anim
elementor animation

### icon
elementor icon selector

### code
code editor

### font
font selector

### repeat
Used to set repeater fields
```xml
<field type="repeat" name="navitems" titlefield="navlabel">
	<repeat type="text" name="navlabel" label="The Label" />
</field>
```
![Repeater Image](https://websitedons.net/assets/repeat.jpg)

### controlgroup
trigger group controls type
```xml
<field 
	type="controlgroup" 
	gtype="bg" 
	gtypes="classic,gradient" 
	name="navbarbg" 
	label="Nav Bar Background" 
	/>
```

### typo
elementor typography group control

```xml
<field
       type="controlgroup"
       gtype="typo"
       name="navfont"
       label="Menu Font"
       />
```



### border
border group control

### shadow
box shadow group controls

### shadowtext
text shadow group controls

### cssfilter
css filter group controls


### table
| n | t | r | p | g |
|---|---|---|---|---|
|   |   |   |   |   |
|   |   |   |   |   |
|   |   |   |   |   |
