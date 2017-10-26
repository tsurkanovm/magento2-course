## Module 3. Rendering Flow
### 3.3.1. In the core files, find and print out the layou t XML for the product viewpage.
### 3.5.1. Create a block extending AbstractBlock and implement the _toHtml() method. Render that block in the new controller
### 3.5.2. Create and render a text block in the controller.
### 3.5.3. Customize the Catalog\Product\View\Description block, implement th beforeToHtml() method, and set a custom description for the product here.
### 3.6.2. Create a template block and a custom template file for it. Render the block in the controller.
### 3.6.3. Customize the Catalog\Block\Product\View\Description block and assign a custom template to it.
### 3.8.1. Add a default.xml layout file to the Training_Render module.
    1.Reference the content.top container.
    2.Add a Magento\Framework\View\Element\Template block with a custom template.
    3.Create your custom template.
    4.Check that the template content is visible on every page.
    
### 3.8.2. Create a new controller action (ex: training_render/layout/onepage).
    1. For that action, choose a single-column page layout using layout XML.
    2. Set a page title using layout XML.

### 3.8.3. Add an arguments/argument node to the block.
1. Set the argument name to background_color.
2. Set the argument value to lightskyblue.
3. In the template, add an inline style attribute to a <div> element:
`style="background_color:<?= $this->getData('background_color') ?>;"`
4. Confirm that the background color is displayed.
### 3.8.4. Change the block color to orange on the product detail pages only.
### 3.8.5. On category pages, move the exercise block to the bottom of the left column.
### 3.8.6. On the custom action you just added, remove the custom block from the content.top container.
### 3.8.7. Using layout XML, add a new link for the custom page you just created to the set of existing links at the top of every page.

## Module 3. Theme exercises
1. Create new theme (Magento/luma as parent)
2. Customize template by new theme.
3. Customize js script (password hints) by new theme.
4. Remove searching field from all pages by new theme.