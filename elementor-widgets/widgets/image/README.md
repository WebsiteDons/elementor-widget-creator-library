# XML Attributes

### selectors
The value is passed to Elementpr's preview update to apply the CSS changes as made in real time.
**Note** while Elementor uses 2 different indices `selector` and `selectors` to define the method of execution, only one(1) is used for this markup process. The interpreter will automatically decide which index to apply based on the value passed.

**Example** to pass a single class that would use `selector`
```xml
<field selectors="{{WRAPPER}} .myclass" />
```
