<?php return array (
  'Illuminate\\Foundation\\Support\\Providers\\EventServiceProvider' => 
  array (
    'App\\Events\\PaymentReceived' => 
    array (
      0 => 'App\\Listeners\\PostPaymentToGl@handle',
    ),
    'App\\Events\\ProductionCompleted' => 
    array (
      0 => 'App\\Listeners\\PostProductionToGl@handle',
    ),
    'App\\Events\\SaleCreated' => 
    array (
      0 => 'App\\Listeners\\PostSaleToGl@handle',
    ),
  ),
);