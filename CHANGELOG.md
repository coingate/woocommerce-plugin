# Changelog
All notable changes to this project will be documented in this file.

v2.3.1
---
* Added "Transfer shopper billing details to payment processor" option. Improves checkout experience by prefilling billing details. Supports compliance with the Travel Rule: https://coingate.com/blog/post/travel-rule-explained

v2.3.0
---
* Added: Support for Block-Based Checkout.


v2.2.0
---
* Deprecated: Currency element.
* PHP Deprecated: Creation of dynamic properties.  
* Fixed: token validation on a callback.
* Fixed: Undefined array key "woocommerce_coingate_test".

v2.1.1
---
* Updates readme

v2.1.0
---
* Added: Ability to send customer email to CoinGate's checkout form.
* Fixed: Send app info from the API auth token validation action.

v2.0.2
---
* Added: WordPress coding standards.
* Added: Github workflow.
* Changed: Updated composer libraries.
* Fixed: Not showing payment settings page [Issue #11]

v2.0.1
---
* **Plugin now requires Wordpress 5.3 version or greater.**
* **Plugin now requires PHP 7.3.0 or greater.**
* **Updated CoinGate PHP library up to 4.1.0.**
* Changed: Plugin refactored from the ground.
* Added: API auth token validation.
* Added: New status - "Do nothing" in the payment gateway settings page.
* Added: New CoinGate payment status - "Confirming".
