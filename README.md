
### What is this repository for? ###

Here you can find an up to date production version of the Magento 2.x Payl8r Payment Method Extension.

The extension adds a new payment method to Magento for customers to pay on finance using Payl8r.


### Installation Guide ###

1. Clone this repository into app/code/Magento folder of your project, this will create a folder Payl8rPaymentGateway with plugin inside.
	Please note all commands below should be run in the root folder of your magento project.

2. Check for if module is disabled (just in case - you can skip this part):
		
		php bin/magento module:status
		
	this should show Magento_Payl8rPaymentGateway in disabled modules
	
3. Enable the module by running:

		php bin/magento module:enable Magento_Payl8rPaymentGateway
		
4. Run module setup (this will add required states and statuses):

		php bin/magento magento setup:upgrade
		
5. Run the Magento re-compile command:

		php bin/magento setup:di:compile

6. Clear Magento cache:

		php bin/magento cache:clean

7. Enable plug-in in the admin dashboard. 

### Configuration ###

Please enter you payl8r username and merchant key into corresponding fields.

Min and max total order values are being populated live from the payl8r server.
There is an internal country validator to check for UK only so plug-in will not show for other countries.

### Who do I talk to? ###

In case of any problems contact me at masitko@gmail.com