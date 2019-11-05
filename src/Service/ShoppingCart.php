<?php
namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;

class ShoppingCart {

    public function __construct(SessionService $session)
    {
        $this->session = $session->session;
        
        if(!$this->session->has('cart'))
        {
            $this->session->set('cart',[]);
        }

        if(!$this->session->has('cart_data'))
        {
            $this->session->set('cart_data',[]);
            $this->session->set('cart_data/total_amount',0);
        }

        if(!$this->session->has('cart_data/total_price'))
        {
            $this->session->set('cart_data/total_price',0.00);
        }
    }

    public function addToCart(Product $product)
    {
        if(!$this->session->has('cart/'.$product->getId()))
        {
            $this->session->set('cart/'.$product->getId().'/id', $product->getId());
            $this->session->set('cart/'.$product->getId().'/name', $product->getName());
            //$this->session->set('cart/'.$product->getId().'/subname', $product->getSubname());
            $this->session->set('cart/'.$product->getId().'/imageFilename', $product->getImageFilename());
			 $this->session->set('cart/'.$product->getId().'/image', $product->getImage());
            $this->session->set('cart/'.$product->getId().'/price', (float) $product->getPrice());
            $this->session->set('cart/'.$product->getId().'/amount', 1);
            $this->session->set('cart/'.$product->getId().'/total_price', $this->session->get('cart/'.$product->getId().'/price'));

            $this->updateCart();

        }
    }

    private function updateCart()
    {
        $total_price = 0;
        $total_amount = 0;

        foreach($this->session->get('cart') as $index => $value)
        {
            $total_price += $this->session->get('cart/'.$index.'/total_price');
            $total_amount += $this->session->get('cart/'.$index.'/amount');
        }

        $this->session->set('cart_data/total_price', (float) sprintf('%.2f',$total_price));
        $this->session->set('cart_data/total_amount', (int) $total_amount);
    }

    public function updateQuantities(Request $request)
    {
        foreach($request->request->get('amounts') as $product => $amount)
        {
            $amount = (int) $amount;
            if($amount < 0 ) $amount = 0;

            $this->session->set('cart/'.$product.'/amount', $amount);
            $this->session->set('cart/'.$product.'/total_price', $amount * $this->session->get('cart/'.$product.'/price'));
        }

        $this->updateCart();
    }

    public function removeFromCart($id)
    {
        $this->session->remove('cart/'.$id);
        $this->updateCart();
    }

    public function deleteCart()
    {
        $this->session->remove('cart');
        $this->session->remove('cart_data');
        // $this->session->clear();
    }
}
