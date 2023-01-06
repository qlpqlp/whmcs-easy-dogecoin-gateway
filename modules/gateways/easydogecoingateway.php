<?php
/**
 * Gateway Name: WHMCS Easy Dogecoin Gateway Module
 * Plugin URI: https://github.com/qlpqlp/whmcs-easy-dogecoin-gateway
 * Description: Acept Dogecoin Payments using simple your Dogecoin Address without the need of any third party payment processor, banks, extra fees | Your Store, your wallet, your Doge.
 * Author: Dogecoin Foundation (inevitable360)
 * Author URI: https://github.com/qlpqlp
 * Version: 69.420.0
 */
 
    if (!defined("WHMCS")) {
        die("This file cannot be accessed directly");
    }

    function easydogecoingateway_config() {

        $configarray = array(
         'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Easy Dogecoin Gateway'
            ),

            // we ask the admin shibe the Doge Address to recive payments
            'dogecoinAddress' => array(
                'FriendlyName' => 'Dogecoin Address',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '',
                'Description' => 'Enter your Dogecoin Address here',
            ),

            // we show the instructions of payment to the shibe
            'dogecoinInstructions' => array(
                'FriendlyName' => 'Instructions',
                'Type' => 'textarea',
                'Rows' => '5',
                'Value' => 'Please pay the exact amount of Dogecoin and send us the Transaction ID by email to be able to check the payment.',
                'Description' => 'Enter your Dogecoin Address here',
            ),

            // we ask the admin shibe Twitter Username to be able to recive payments using Twitter MyDoge or Sodoge
            'twitterUsername' => array(
                'FriendlyName' => 'Twitter Username',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '',
                'Description' => 'Enter your Twitter Username here',
            ),

            // we ask the admin shibe is he wants to enable MyDoge Twitter Payments
            'myDogetip' => array(
                'FriendlyName' => 'MyDoge Tip Payment',
                'Type' => 'yesno',
                'Description' => 'Tick to enable MyDoge Twitter Payments',
            ),

            // we ask the admin shibe is he wants to enable SoDoge Twitter Payments
            'soDogetip' => array(
                'FriendlyName' => 'SoDoge Tip Payment',
                'Type' => 'yesno',
                'Description' => 'Tick to enable SoDoge Twitter Payments',
            ),

        );

        return $configarray;

    }

    // show the Dogecoin Payment information to the shibe
    function easydogecoingateway_link($params) {
        global $_LANG;

            // if the currency is not DOGE we try to convert Fiat Currency into Dogecoin
            if (strtolower($params['currency']) != "doge"){
                $total = convert_to_crypto($params['amount'],$params['currency']);
                // if for any reason there is a error converting into Dogecoin, we do not allow to finalize the transaction
                if ($total == 0){ return null; }
            }else{
                $total = $params['amount'];
            }

            // we initialize the code to display on the Shibe webpage
            $code = '';

            // we check if MyDoge or Sodoge are enable to display a message on top
            if (isset($params['myDogetip']) or isset($params['soDogetip'])){
                $code .= '<div class="row"><div style="border-top: 5px solid  rgba(51, 153, 255, 1); border-top-left-radius: 15px; border-top-right-radius: 15px; padding: 10px"><div style="text-align: center">Pay directly in Dogecoin using <b>Twitter</b> Doge Wallet Bots!</div>';
            };

            // we check if MyDoge is enable to display the Button to pay using Twitter
            if (isset($params['myDogetip'])){
                $mydoge_pay = "%0a%0a🥳🎉🐶🔥🚀%0a@MyDogeTip%20tip%20".$params['twitterUsername']."%20".$total."%20Doge%20";
                $mydoge_wallet_link = 'https://twitter.com/intent/tweet?text='.$params['twitterUsername'].'%20 TwitterPay Order id:'.$params['invoiceid'].$mydoge_pay.'%0a%0a'.$_SERVER['HTTP_HOST'].'%0a&hashtags=Doge,Dogecoin';
                $code .='<a href="'.$mydoge_wallet_link.'" target="_blank" style="padding: 15px"><div style="background: rgba(51, 153, 255, 1); border-radius: 15px; color: rgba(255, 255, 255, 1); text-align: center">Pay using Twitter MyDoge</div></a>';
            };

            // we check if SoDoge is enable to display the Button to pay using Twitter
            if (isset($params['soDogetip'])){
                $sodoge_pay = "%0a%0a🥳🎉🐶🔥🚀%0a@SoDogeTip%20tip%20".$params['twitterUsername']."%20".$total."%20Doge%20";
                $sodoge_wallet_link = 'https://twitter.com/intent/tweet?text='.$params['twitterUsername'].'%20 TwitterPay Order id:'.$params['invoiceid'].$sodoge_pay.'%0a%0a'.$_SERVER['HTTP_HOST'].'%0a&hashtags=Doge,Dogecoin';
                $code .='<a href="'.$sodoge_wallet_link.'" target="_blank" style="padding: 15px"><div style="background: rgba(51, 153, 255, 1); border-radius: 15px; color: rgba(255, 255, 255, 1); text-align: center">Pay using Twitter SoDoge</div></a>';
            };

            // we display all formated code including the QR code, Doge Address and instructions to pay
            $code .='<div class="row"><div style="border-top: 5px solid rgba(204, 153, 51, 1); border-top-left-radius: 15px; border-top-right-radius: 15px; padding: 10px">'.$params['dogecoinInstructions'].'</div><div class="col" style="float:none;margin:auto; text-align: center;max-width: 425px; border: 2px solid rgba(204, 153, 0, 1); border-radius: 15px; padding: 10px;"><div style="background-color: rgba(204, 153, 0, 1); padding: 10px; border-radius: 15px; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px"><h2 style="font-size: 20px; color: rgba(0, 0, 0, 1); font-weight: bold">Ð '. $total . '</h2></div><img id="qrcode" src="//chart.googleapis.com/chart?cht=qr&chs=400x400&chl=' . $params['dogecoinAddress'] . '&amp;size=400x400" alt="" title="Such QR Code!" style="max-width: 400px !important"/><div style="background-color: rgba(204, 153, 0, 1); padding: 10px; border-radius: 15px; border-top-left-radius: 0px; border-top-right-radius: 0px; color: rgba(0, 0, 0, 1)">' . $params['dogecoinAddress'] . '</div></div></div> ';

        // we display all to the Shibe
        return $code;

    }

    // Convert fiat Money into Dogecoin Money
    function convert_to_crypto($value, $from) {

        $response = file_get_contents("https://api.coingecko.com/api/v3/coins/markets?vs_currency=".strtolower($from)."&ids=dogecoin&per_page=1&page=1&sparkline=false");
        $price = json_decode($response);
        $response = $value / $price[0]->current_price;
        $response = number_format($response, 2, '.', '');
           if ($response > 0)
          return trim($response);

         return 0;
    }
?>