## Module 3. Rendering Flow
### 3.8.3. Add an arguments/argument node to the block.
1. Set the argument name to background_color.
2. Set the argument value to lightskyblue.
3. In the template, add an inline style attribute to a <div> element:
`style="background_color:<?= $this->getData('background_color') ?>;"`
4. Confirm that the background color is displayed.
### 3.8.4. Change the block color to orange on the product detail pages only.