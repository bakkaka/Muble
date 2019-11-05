<?php
namespace App\Service;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use PayPal\Api\{Payer,Item,ItemList,Details,Amount,Transaction,RedirectUrls,Payment,PaymentExecution};

class PayPal {

    public function __construct(SessionService $session, UrlGeneratorInterface $router, RequestStack $requestStack, ShoppingCart $shoppingCart)
    {
        $this->session = $session->session;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->shoppingCart = $shoppingCart;
    }

    public function createPayment()
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        foreach($this->session->get('cart') as $product)
        {
            $items[] = $item = new Item();
            $item->setName($product['name'])
                ->setCurrency('USD')
                ->setQuantity($product['amount'])
                ->setPrice($product['price']);
        }

        $itemList = new ItemList();
        $itemList->setItems($items);

        $details = new Details();
        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($this->session->get('cart_data/total_price'));

        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($this->session->get('cart_data/total_price'))
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->router->generate('execute-payment',[],UrlGeneratorInterface::ABSOLUTE_URL))
            ->setCancelUrl($this->router->generate('cart',[],UrlGeneratorInterface::ABSOLUTE_URL));

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $_ENV['PAYPAL_CLIENT_ID'],     // ClientID
                $_ENV['PAYPAL_CLIENT_SECRET']     // ClientSecret
            )
        );
        $payment->create($apiContext);
        $approvalUrl = $payment->getApprovalLink();
        return $approvalUrl;
    }

    public function executePayment()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $_ENV['PAYPAL_CLIENT_ID'],     // ClientID
                $_ENV['PAYPAL_CLIENT_SECRET']      // ClientSecret
            )
        );

        $request = $this->requestStack->getCurrentRequest();

        $paymentId = $request->query->get('paymentId');
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->query->get('PayerID'));

        $transaction = new Transaction();
        $amount = new Amount();
        $details = new Details();

        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($this->session->get('cart_data/total_price'));

        $amount->setCurrency('USD');
        $amount->setTotal($this->session->get('cart_data/total_price'));
        $amount->setDetails($details);
        $transaction->setAmount($amount);

        $execution->addTransaction($transaction);

        $result = $payment->execute($execution, $apiContext);
        $result = $result->toJSON();
        $result = json_decode($result);

        if($result->state === 'approved')
        {
            ($result);
            // ... here save order in the database
            $this->shoppingCart->deleteCart();
        }
    }

}
