**Documentation**

**Installation**

The installation is quite simple.

1. Configure the concrete5 mail service (if you don't already have)
	1. Setup the SMTP server. If you want to add mail services like Mailgun, Sendgrid etc. you can install the [Mail Service Integration](https://www.concrete5.org/marketplace/addons/mail-service-integration) add-on from Justin978.
	2. Optional: Configure [the sender address](https://documentation.concrete5.org/developers/framework/sending-mail/configure-email-sender-addresses). If you don't want to edit the configuration files manually you can take use of the [Handyman](https://www.concrete5.org/marketplace/addons/handyman) add-on from mlocati.
2. Install the add-on
1. Create at least one mailing list
2. Embed a subscription block type of a page of your choice
3. Embed a unsubscription block type o page of your choice
4. Go to add-on settings and set the legal informations and the where the unsubscription block type is embedded
5. You're done. Have fun sending newsletters with Simple Newsletter!

**Attributes**

You can easily extend the subscription form with custom fields and use the values to personalzie your newsletters

1. Go to the dashboard page subscribers > attributes
2. Add the attributes you want

To learn more about working with attributes [click here](https://documentation.concrete5.org/user-guide/editors-reference/dashboard/pages-and-themes/attributes).

**Sending Newsletters**

1. Create a new campaign
2. Add name (for internal usage), a subject and a body
3. You can personalize your newsletter campaigns by using placeholders. To integrate placeholders click the placeholder icon in the editors toolbar
4. Click on save
5. If you want to send the campaign click on add to send queue
6. Now run the send newsletter job in the automated jobs dashboard page or with the CLI task `simple-newsletter:send-newsletter`

**Advanced: Customizing the mail layout**

If you want to customize the html markup of all outgoing emails you can do so by overriding the mail template.

Copy the mail template from packages/simple_newsletter/elements/newsletter_template.php to application/elements/newsletter_template.php and perform your changes there.

**Advanced: Events**

There are some events in this package that you can hook into.

To learn more about hooking into application events click [here](https://documentation.concrete5.org/developers/framework/application-events/hooking-application-events).

These are the available application events:

- on\_newsletter\_subscribe
- on\_newsletter\_unsubscribe
- on\_newsletter\_subscription\_confirm

Furthermore all relevant informations are stored in the concrete5 logs. Click [here](https://documentation.concrete5.org/developers/framework/logging/overview) to learn how to working with logs.

**Advanced: Custom Templates**

If you want to customize the markup of the subscription or unsubscription form block type you can do so by adding custom templates.

Click [here](https://documentation.concrete5.org/developers/working-with-blocks/working-with-existing-block-types/creating-additional-custom-view-templates) to learn more about creating custom templates.
