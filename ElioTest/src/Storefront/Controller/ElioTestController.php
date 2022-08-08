<?php

namespace ElioTest\Storefront\Controller;

use ElioTest\Service\SearchService;
use ElioTest\Service\ValidateFormService;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class ElioTestController extends StorefrontController
{
    private SearchService $searchService;
    private ValidateFormService $validateFormService;
    private LineItemFactoryRegistry $factory;
    private CartService $cartService;


    public function __construct(
        SearchService $searchService,
        ValidateFormService $validateFormService,
        LineItemFactoryRegistry $factory,
        CartService $cartService
    ) {
        $this->searchService = $searchService;
        $this->validateFormService = $validateFormService;
        $this->factory = $factory;
        $this->cartService = $cartService;

    }

    /**
     * @Route("/product/add/multiple", name="multiple.add.form", methods={"GET"}, defaults={"_routeScope"={"storefront"}})
     */
    public function getForm(
        Context $context
    ): Response {
        $products = $this->searchService->searchAvailableProducts($context);

        $currency = Defaults::CURRENCY;

        $maxProductCount = getenv('MULTIPLE_PRODUCTS_COUNT');

        return $this->renderStorefront('@ElioTest/storefront/page/form.html.twig',
            [
                'products' => $products,
                'currency' => $currency,
                'max' => $maxProductCount
            ]);
    }

    /**
     * @Route("/checkout/cart", name="multiple.add.to.cart", methods={"POST"}, defaults={"_routeScope"={"storefront"}})
     * @throws \Exception
     */
    public function addToCart(
        Request $request,
        SalesChannelContext $context,
        Cart $cart
    ): Response {
        $maxProductCount = getenv('MULTIPLE_PRODUCTS_COUNT');

        $this->validateFormService->validateForm($request, (int)$maxProductCount);

        for ($i = 1; $i <= $maxProductCount; $i++) {
            if (!$id = $request->get("item{$i}")) {
                continue;
            }
            $item = $this->searchService->searchAvailableProducts(Context::createDefaultContext(), $id);

            $quantity = (int)$request->get("qnt{$i}");

            if (($availableStock = $item->jsonSerialize()[ 'elements' ][ $id ]->getAvailableStock()) < $quantity) {
                $quantity = $availableStock;
            }

            $lineItem = $this->factory->create(
                [
                    'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
                    'referencedId' => $id,
                    'id' => $id,
                    'quantity' => $quantity,
                ],
                $context);

            $this->cartService->add($cart, $lineItem, $context);
        }
        return $this->redirect('/checkout/cart');
    }
}
