<?php
$simple = "<Order ID="'.$orderID.'" Lang="'.$Lang.'" Timeout="'.$Timeout.'" Signature="testsignature" SigntureType="'.$SignType.'"><MerchantInfo><ID>'.$MerchantID.'</ID><Name>'.$MerchantName.'</Name><Address>'.$MerchantAddress.'</Address><City>'.$MerchantCity.'</City><Country>'.$MerchantCountry.'</Country><BackUrl>'.$MerchantBackURL.'</BackUrl></MerchantInfo><ShippingInfo><Name>'.$ShipName.'</Name><Address>'.$ShipAddress.'</Address><City>'.$ShipCity.'</City><Postal>'.$ShipPostal.'</Postal><Country>'.$ShipCountry.'</Country><Email>'.$ShipEmail.'</Email><Phone>'.$ShipPhone.'</Phone><Phone2>'.$ShipPhone2.'</Phone2><PAK>'.$ShipPAK.'</PAK></ShippingInfo><BillingInfo><FirstName>'.$BillingName.'</FirstName><LastName>'.$BillingLast.'</LastName><Email>'.$BillingEmail.'</Email></BillingInfo><TotalAmounts CurCode="'.$CurCode.'"><Itemsx>'.$ItemsTA.'</Itemsx><Discount>'.$DiscountTA.'</Discount><Shipping>'.$ShippingTA.'</Shipping><Tax>'.$TaxTA.'</Tax><Total>'.$TotalTA.'</Total></TotalAmounts><Items><Item Ordering="'.$ItemsOrder.'"><Description>'.$ItemsDescription.'</Description><Code>'.$ItemsCode.'</Code><UnitPrice>'.$ItemsUnitPrice.'</UnitPrice><Quantity>'.$ItemsQuantity.'</Quantity><Discount>'.$ItemsDiscount.'</Discount><TaxPercent>'.$ItemsTaxPercent.'</TaxPercent><TaxAmount>'.$ItemsTaxAmount.'</TaxAmount><ShippingPrice>'.$ItemsShippingPrice.'</ShippingPrice><TotalPrice>'.$ItemsTotalPrice.'</TotalPrice></Item></Items></Order>";
$p = xml_parser_create();
xml_parse_into_struct($p, $simple, $vals, $index);
xml_parser_free($p);
echo "Index array\n";
print_r($index);
echo "\nVals array\n";
print_r($vals);
?>
