<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Catalog\Lists\Items;


/**
 * Default implementation of catalog list item section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/catalog/lists/items/standard/subparts
	 * List of HTML sub-clients rendered within the catalog list items section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $subPartPath = 'client/html/catalog/lists/items/standard/subparts';
	private $subPartNames = array();
	private $tags = array();
	private $expire;
	private $view;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null )
	{
		$view = $this->setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getBody( $uid, $tags, $expire );
		}
		$view->itemsBody = $html;

		/** client/html/catalog/lists/items/standard/template-body
		 * Relative path to the HTML body template of the catalog list items client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/lists/items/standard/template-header
		 */
		$tplconf = 'client/html/catalog/lists/items/standard/template-body';
		$default = 'catalog/lists/items-body-default.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string|null String including HTML tags for the header on error
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		$view = $this->setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader( $uid, $tags, $expire );
		}
		$view->itemsHeader = $html;

		/** client/html/catalog/lists/items/standard/template-header
		 * Relative path to the HTML header template of the catalog list items client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page header
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the templates directory (usually
		 * in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page head
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/lists/items/standard/template-body
		 */
		$tplconf = 'client/html/catalog/lists/items/standard/template-header';
		$default = 'catalog/lists/items-header-default.php';

		return $view->render( $this->getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		/** client/html/catalog/lists/items/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog list items html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/html/common/decorators/default" before they are wrapped
		 * around the html client.
		 *
		 *  client/html/catalog/lists/items/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/lists/items/decorators/global
		 * @see client/html/catalog/lists/items/decorators/local
		 */

		/** client/html/catalog/lists/items/decorators/global
		 * Adds a list of globally available decorators only to the catalog list items html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/catalog/lists/items/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/lists/items/decorators/excludes
		 * @see client/html/catalog/lists/items/decorators/local
		 */

		/** client/html/catalog/lists/items/decorators/local
		 * Adds a list of local decorators only to the catalog list items html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Catalog\Decorator\*") around the html client.
		 *
		 *  client/html/catalog/lists/items/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Catalog\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/lists/items/decorators/excludes
		 * @see client/html/catalog/lists/items/decorators/global
		 */

		return $this->createSubClient( 'catalog/lists/items/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames()
	{
		return $this->getContext()->getConfig()->get( $this->subPartPath, $this->subPartNames );
	}


	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modifyBody( $content, $uid )
	{
		$content = parent::modifyBody( $content, $uid );

		return $this->replaceSection( $content, $this->getView()->csrf()->formfield(), 'catalog.lists.items.csrf' );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function setViewParams( \Aimeos\MW\View\Iface $view, array &$tags = array(), &$expire = null )
	{
		if( !isset( $this->view ) )
		{
			$view->itemPosition = ( $this->getProductListPage( $view ) - 1 ) * $this->getProductListSize( $view );

			if( $this->getContext()->getConfig()->get( 'client/html/catalog/list/basket-add', false ) )
			{
				$view = $this->addSelectionProducts( $view, $tags, $expire );
				$view = $this->addConfigAttributes( $view, $tags, $expire );
			}

			$this->view = $view;
		}

		return $this->view;
	}


	/**
	 * Adds the necessary view parameters for adding selection products to the basket
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addSelectionProducts( \Aimeos\MW\View\Iface $view, array &$tags = array(), &$expire = null )
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$subProdMap = $prodMap = $prodIds = array();

		foreach( (array) $view->get( 'listProductItems', array() ) as $product )
		{
			if( $product->getType() === 'select' )
			{
				foreach( $product->getListItems( 'product', 'default' ) as $listItem )
				{
					$prodMap[$listItem->getParentId()][] = $listItem->getRefId();
					$prodIds[] = $listItem->getRefId();
				}
			}
		}


		$domains = array( 'text', 'price', 'media', 'attribute' );
		$domains = $config->get( 'client/html/catalog/detail/basket/selection/domains', $domains );

		$controller = \Aimeos\Controller\Frontend\Factory::createController( $context, 'catalog' );
		$subproducts = $controller->getProductItems( $prodIds, $domains );
		$attrIds = $prodDeps = $attrDeps = $attrTypeDeps = array();

		foreach( $prodMap as $prodId => $list )
		{
			foreach( $list as $subProdId )
			{
				if( isset( $subproducts[$subProdId] ) )
				{
					$subProduct = $subproducts[$subProdId];
					$subProdMap[$prodId][$subProdId] = $subProduct;

					foreach( $subProduct->getRefItems( 'attribute', null, 'variant' ) as $attrId => $attrItem )
					{
						$attrTypeDeps[$prodId][$attrItem->getType()][$attrId] = $attrItem->getPosition();
						$attrDeps[$prodId][$attrId][] = $subProdId;
						$prodDeps[$prodId][$subProdId][] = $attrId;
						$attrIds[$attrId] = null;
					}
				}
			}

			ksort( $attrTypeDeps[$prodId] );
		}

		$this->addMetaItem( $subproducts, 'product', $this->expire, $this->tags );
		$this->addMetaList( array_keys( $subproducts ), 'product', $this->expire );


		$attrManager = $controller->createManager( 'attribute' );

		$search = $attrManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'attribute.id', array_keys( $attrIds ) ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$domains = $config->get( 'client/html/catalog/list/items/domains-attributes', array( 'text', 'price', 'media' ) );
		$attributes = $attrManager->searchItems( $search, $domains );

		$this->addMetaItem( $attributes, 'attribute', $this->expire, $this->tags );
		$this->addMetaList( array_keys( $attributes ), 'attribute', $this->expire );


		$view->itemsSelectionProducts = $subProdMap;
		$view->itemsSelectionProductDependencies = $prodDeps;
		$view->itemsSelectionAttributeDependencies = $attrDeps;
		$view->itemsSelectionAttributeTypeDependencies = $attrTypeDeps;
		$view->itemsSelectionAttributeItems = $attributes;

		return $view;
	}


	/**
	 * Adds the necessary view parameters for adding config attributes to the basket
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addConfigAttributes( \Aimeos\MW\View\Iface $view, array &$tags = array(), &$expire = null )
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$attributeTypes = array();

		foreach( (array) $view->get( 'listProductItems', array() ) as $product )
		{
			foreach( $product->getListItems( 'attribute' ) as $listItem ) {
				$attrIds[$listItem->getRefId()] = null;
			}
		}


		/** client/html/catalog/list/items/domains-attributes
		 * A list of domain names whose items should be available for the attributes
		 * in the "items" part of the catalog list view templates
		 *
		 * The templates rendering attributes usually add images, texts and
		 * maybe prices associated to each item. If you want to display
		 * additional content like the attributes, you can configure your own
		 * list of domains (attribute, media, price, product, text, etc. are
		 * domains) whose items are fetched from the storage.
		 *
		 * Please keep in mind that the more domains you add to the
		 * configuration, the more time is required for fetching the content!
		 *
		 * @param array List of domain names
		 * @since 2016.01
		 * @category Developer
		 */
		$domains = $config->get( 'client/html/catalog/list/items/domains-attributes', array( 'text', 'price', 'media' ) );

		$controller = \Aimeos\Controller\Frontend\Factory::createController( $context, 'catalog' );
		$attrManager = $controller->createManager( 'attribute' );

		$search = $attrManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'attribute.id', array_keys( $attrIds ) ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attrItems = $attrManager->searchItems( $search, $domains );

		$this->addMetaItem( $attrItems, 'attribute', $this->expire, $this->tags );
		$this->addMetaList( array_keys( $attrItems ), 'attribute', $this->expire );


		foreach( (array) $view->get( 'listProductItems', array() ) as $prodId => $product )
		{
			foreach( $product->getRefItems( 'attribute', null, 'config' ) as $id => $attribute )
			{
				if( isset( $attrItems[$id] ) ) {
					$attributeTypes[$prodId][$attrItems[$id]->getType()][$id] = $attrItems[$id];
				}
			}
		}

		$view->itemsAttributeConfigItems = $attributeTypes;

		return $view;
	}
}