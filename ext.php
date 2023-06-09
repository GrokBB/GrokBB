<?php
// the GrokBB config
require_once('cfg.php');

// the Stripe config
require_once(SITE_BASE_LIB . 'stripe-3.12.1' . DIRECTORY_SEPARATOR . 'init.php');

$_SESSION['user']->isAdmin = true;

if ($_GET['event'] == 'invoice.payment_failed') {
    try {
        \Stripe\Stripe::setApiKey((CC_LIVE) ? CC_LIVE_SK : CC_TEST_SK);
    
        $stripeJSON = @file_get_contents('php://input');
        $stripe = json_decode($stripeJSON);
        
        $verifyEvent = \Stripe\Event::retrieve($stripe->id);
        
        if (is_object($verifyEvent)) {
            $stripeId = $verifyEvent->data->object->id;
            $board = $GLOBALS['db']->getOne('board', array('stripe_id' => $stripeId, 'stripe_cancelled' => 0));
            
            if ($board) {
                $boardObject = new \GrokBB\Board;
                $boardObject->cancel($board->id);
            }
        }
        
        http_response_code(200);
    } catch(\Stripe\Error\Base $e) {
        $body = $e->getJsonBody();
        
        $stripeError = "HTTP Status: " . $e->getHttpStatus() . "\nStripe Error: " . print_r($body['error'], true);
        
        error_log($stripeError);
    }
}
?>