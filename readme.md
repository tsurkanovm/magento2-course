### Unit 1. Preparation & Configuration

#### 1.3.1. Create a new module. Make a mistake in its config. Create a second module dependent on the first.
#### 1.6.2: Object Manager
Go back to the two modules created you created in Exercise 1.3.1, Unit1_FirstModule and Unit1_SecondModule*.
In Unit1_FirstModule, create the folder “MagentoU”. In this folder, create the class “Test”. The code to be used is given
below.
#### 1.7.2: Plugins
For the class... Magento\Catalog\Model\Product and the method... getPrice():
Create a plugin that will modify price (afterPlugin).
1. Customize Magento\Theme\Block\Html\Footer class, to replace the body of the getCopyright() method with your
implementation. Return a hard-coded string: “Customized copyright!”
2. Customize Magento\Theme\Block\Html\Breadcrumbs class, addCrumb() method, so that every crumbName is
transformed into:
$crumbName . “(!)”
#### 1.8.1. In your module, create an observer to the event controller_action_predispatch. 
Get the URL from the request object request->getPathInfo(). Log it into the file.

### Unit 2. Request Flow

#### 2.2.1. Find a place in the code where output is flushed to the browser. Create an extension that captures and logs the file-generated page HTML. (“Flushing output” means a “send” call to the response object.)
#### 2.3.1. Create an extension that logs into the file list of all available routers into a file.
#### 2.3.2. Create a new router which “understands” URLs like /frontName-actionPath-action and converts them to /frontName/actionPath/action
#### 2.3.3. Modify Magento so a “Not Found” page will forward to the home page.
#### 2.5.1. Create a frontend controller that renders “HELLO WORLD”
#### 2.5.2. Customize the catalog product view controller using plugins and preferences.
#### 2.5.3. Create an adminhtml controller that allows access only if the GET parameter “secret” is set.

### Unit 3. Rendering Flow

#### 3.3.1. In the core files, find and print out the layou t XML for the product viewpage.
#### 3.5.1. Create a block extending AbstractBlock and implement the _toHtml() method. Render that block in the new controller
#### 3.5.2. Create and render a text block in the controller.
#### 3.5.3. Customize the Catalog\Product\View\Description block, implement th beforeToHtml() method, and set a custom description for the product here.
#### 3.6.2. Create a template block and a custom template file for it. Render the block in the controller.
#### 3.6.3. Customize the Catalog\Block\Product\View\Description block and assign a custom template to it.
#### 3.8.1. Add a default.xml layout file to the Training_Render module.
    1.Reference the content.top container.
    2.Add a Magento\Framework\View\Element\Template block with a custom template.
    3.Create your custom template.
    4.Check that the template content is visible on every page.
    
#### 3.8.2. Create a new controller action (ex: training_render/layout/onepage).
    1. For that action, choose a single-column page layout using layout XML.
    2. Set a page title using layout XML.

#### 3.8.3. Add an arguments/argument node to the block.
1. Set the argument name to background_color.
2. Set the argument value to lightskyblue.
3. In the template, add an inline style attribute to a <div> element:
`style="background_color:<?= $this->getData('background_color') ?>;"`
4. Confirm that the background color is displayed.
#### 3.8.4. Change the block color to orange on the product detail pages only.
#### 3.8.5. On category pages, move the exercise block to the bottom of the left column.
#### 3.8.6. On the custom action you just added, remove the custom block from the content.top container.
#### 3.8.7. Using layout XML, add a new link for the custom page you just created to the set of existing links at the top of every page.

### Unit 3. Theme exercises 

1. Create new theme (Magento/luma as parent)
2. Customize template by new theme.
3. Customize js script (password hints) by new theme.
4. Remove searching field from all pages by new theme.

### Unit 3. JavaScript exercises
1. Create an extension which will print into the console a number of shown attributes in the "More Information" tab
on the product details page.
2. Create an extension that alerts the full action name on every page.
3. Dump the data (to console) passed to the customer form (front - customer/account/login).
4. Dump the data (to the file) passed to the customer form (admin - /customer/index/edit).
5. Create an extension that writes "Hello World" at the top of a mini-cart popup.
