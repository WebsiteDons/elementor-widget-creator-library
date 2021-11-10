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
