=== Add coupon by link / URL coupons / Add coupon by Qr Code for Woocommerce ===
Contributors: rajeshsingh520
Donate link: piwebsolution.com
Tags: coupons, url coupons, qr code, qrcode
Requires at least: 3.0.1
Tested up to: 6.6.1
License: GPLv2 or later
Stable tag: 1.1.62
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adding coupons by url coupon, restrict coupon by product attribute, restrict coupon by user role or restrict by user email id

== Description ==

WooCommerce URL coupons let you give your customers a coupon link to apply a coupon. Using URL coupons your customers can apply coupons via visiting a link.

Use WooCommerce URL coupons on buttons, images and text. Show them on your sidebar, ads, email marketing, blog posts – basically wherever you can put a link you can put a URL coupon!

At alow offer multiple condition to control the coupon usage. You can restrict the coupon by product attribute, user role, user email id, payment method.

= Its working =

All the WooCommerce coupons can be applied by your customers by visiting a link with coupon code embedded in the URL

`
Example link: http://abc.com/?apply_coupon=[coupon code]
`

* You can change the url coupon key **?apply_coupon=** from the plugin setting
* You can also disable the WooCommerce default coupon insertion field present in the cart and checkout page
* You can set a different message for the coupon from the message tab in the coupon, or you can use the global message set in the plugin setting
* You can set a message that will be applied when user first lands on your website with the url, and the coupon is not yet applied as its a conditional coupon
* Set a message to inform the customer that the coupon is added, but since condition of the coupon are not yet satisfied it is not applied, once the condition of the coupon get satisfied the coupon is applied.You can even describe the condition of the coupon in this message. As you can set different message for different coupon.
* This URL coupons plugin will work even for the guest users.
* This URL coupons plugin will work for the conditional coupon as well.
* Specify product to auto add to the cart when URL coupon is clicked
* You can specify different set of auto add product for different coupons
* Plugin also supports Coupon QR code. It will generate the Qr code for the coupon.
* Option to apply the coupon when a specific product is added to the cart
* You can assign a coupon to the category, so when a product from that category is added to the cart the coupon will be applied automatically

= Add to cart coupon =
[youtube https://www.youtube.com/watch?v=kqSl8ze3HZI]


= How URL coupons will handle conditional coupon =
* When the URL coupon has conditional coupon then it will add the coupon in the user session and notify the customer that coupon is added and it will be applied when coupon conditions are satisfied
* Once coupon condition are satisfied the coupon get applied automatically

 = Attribute-Based Coupon Restrictions =
 With the Attribute-Based Coupon Restrictions, you can now create more targeted promotions by applying or excluding coupons based on specific product attributes. This feature is perfect for store owners looking to fine-tune their discount strategies and ensure that promotions are applied exactly where they are intended.

**Targeted Promotions:** Apply coupons to products with specific attributes, such as color, size, or any other custom attribute, ensuring your promotions reach the right products.
**Exclusion Control:** Exclude products with certain attributes from coupon eligibility, giving you precise control over your discount campaigns.
**Easy Management:** Easily add, manage, and update attribute restrictions directly from the coupon settings in your WooCommerce dashboard.

== Used in Order: Tracking Feature ==

This feature enhances the WooCommerce coupon management by adding a new "Used in order" column in the coupons list view. Each coupon now includes a clickable link that provides insight into its usage history. Upon clicking the link, users can view a detailed list of orders in which the coupon has been applied. This feature offers transparency and enables store owners to track the effectiveness and history of each coupon effortlessly.

== Exclude Email Restrictions for Coupons ==
This feature enhances coupon management by allowing exclusion of specific email addresses or entire domains from coupon application. You can create a list of email IDs that coupons should not apply to, and use wildcard exceptions like *@gmail.com to block coupons for entire domains. For example, you can exclude xyz@ps.com, *@yahoo.com, abc@gmail.com, and more. This feature provides greater control over coupon usage and ensures that discounts are applied only to eligible customers.

== Payment Methods Restriction for Coupons ==
Ability to restrict coupon usage based on specific payment methods. This feature empowers you to tailor promotions by limiting coupon applicability to selected payment methods, ensuring greater control over discount distribution and promotional strategies.

== User Role Restrictions for Coupons ==
Introducing an advanced feature in our WooCommerce plugin, designed to provide precise control over coupon usage based on user roles. Now, you can effectively manage promotions by applying coupons exclusively to selected user roles or excluding them from specific user roles.


== Store Credit Coupon ==
Store Credit coupons offer a versatile and customer-friendly way to manage discounts and returns within your WooCommerce store. This coupon type allows you to issue credit that customers can use for future purchases, providing a seamless shopping experience and encouraging repeat business.

Customers can use part or all of their store credit in a single purchase. If the cart total is less than the store credit amount, the remaining balance can be used for future orders.

Customers can view their remaining store credit balance directly in the cart, enhancing transparency and encouraging further engagement.

= Issue Refunds as Store Credit =
With this feature, you can now issue full or partial refunds as store credit directly from the order edit page. This provides a convenient and flexible way to manage refunds and retain customer loyalty. By issuing refunds as store credit, you can effectively manage returns and keep customers engaged with your store.


== Frequently Asked Questions ==
= Auto add product to user cart when URL coupon is applied =
Yes, you can specify different set of product that should be auto added to the user cart when a certain url coupon link is clicked or visited

= Plugin will generate the QR code of the coupon =
Yes, Qr code will be generated for the coupon, your customer can scan the Qr code and coupon will be applied

= Can I download Qr Code =
Yes you can download Qr code 

= Apply coupon when a specific product is added to the cart =
Yes, you can configure a product to apply coupon, when that product is added to the cart the coupon will be applied

= Can I assign add to cart coupon from the category =
Yes you can assign a coupon to a category, when a product from that category is added to the cart the coupon will be applied automatically

= HPOS compatible =
Yes plugin support HPOS

= What are Attribute-Based Coupon Restrictions? =
Attribute-Based Coupon Restrictions allow you to apply or exclude coupons based on specific product attributes in your WooCommerce store. This feature gives you greater control over which products are eligible for discounts.

= How do I enable Attribute-Based Coupon Restrictions for a coupon? =
To enable Attribute-Based Coupon Restrictions, go to the coupon edit screen in your WooCommerce dashboard. Navigate to the "Attribute Restrictions" tab, where you can select the attributes you want to include or exclude from the coupon eligibility.

= Can I use multiple attributes for a single coupon? =
Yes, you can select multiple attributes to include or exclude when setting up your coupon restrictions. This allows you to create complex discount rules tailored to your store’s needs.

= What happens if a product has multiple attributes? =
If a product has multiple attributes, the coupon will be applied or excluded based on the attribute conditions you set. For example, if you include the attribute "Color: Red" and exclude "Size: Large", the coupon will apply to products that are red but not large.

= Can I combine Attribute-Based Coupon Restrictions with other coupon restrictions? =
Yes, you can combine Attribute-Based Coupon Restrictions with other coupon settings, such as minimum spend, product categories, and usage limits, to create comprehensive discount rules.

= Will the Attribute-Based Coupon Restrictions work with variable products? =
Yes, the restrictions will work with variable products. You can target specific variations based on their attributes, ensuring that the coupon is applied correctly to the desired variations.

= How do I troubleshoot if the coupon is not applying as expected? =
If the coupon is not applying as expected, double-check the attribute restrictions you have set. Ensure that the products in your cart meet the attribute conditions specified in the coupon settings. Additionally, make sure there are no conflicting restrictions that might prevent the coupon from being applied.

= Is there a limit to the number of attributes I can restrict? =
There is no limit to the number of attributes you can restrict. You can include or exclude as many attributes as needed to meet your promotional goals.

= How can I view which orders a coupon has been used in? =
Navigate to the WooCommerce Coupons section in your WordPress admin panel. Look for the "Used in order" column, which displays a link for each coupon. Clicking this link will reveal a list of orders where the coupon has been applied.

= How do I exclude specific email addresses from using a coupon? =
You can exclude specific email addresses by listing them in the coupon Usage restriction > Exclude email section. Separate multiple email addresses with commas. For example, xyz@example.com, abc@example.com.

= Can I exclude entire domains from using a coupon? =
Yes, you can exclude entire domains by using wildcard exceptions. For instance, *@gmail.com will exclude all email addresses ending with @gmail.com from using the coupon.

= What happens if an excluded email attempts to use the coupon? =
If a customer tries to apply the coupon with an excluded email address or domain, the coupon will not be applied, and an error message will be displayed during checkout. If user has not yet added his email id and tries to add the coupon the coupon will get applied but when the user adds his email id and it matches the excluded email id the coupon will be made to 0 and will be removed on checkout.

= Can I combine specific email exclusions with domain exceptions? =
Yes, you can combine both specific email exclusions and domain exceptions. This allows for flexible management of coupon restrictions based on customer email addresses. E.g. test@example.com, john@example.com, *@gmail.com, *@yahoo.com.

= Can I restrict a coupon to apply only for specific payment methods? =
Yes, our plugin now allows you to restrict coupon usage to selected payment methods. Simply choose the payment methods you want to include when setting up your coupon.

= Can I exclude certain user roles from using a coupon? =
Yes, you can now restrict coupon usage based on user roles. Choose the user roles you want to include or exclude when setting up your coupon to control who can apply the discount.

= Can I set different messages for different coupons? =
Yes, you can set unique messages for each coupon. This allows you to tailor the messaging based on the specific promotion or discount associated with each coupon.

= What kind of messages can I customize? =
You can customize various types of messages, such as:

* A success message indicating the coupon has been applied in there session when they visit the site using URL coupon
* A conditional message informing the customer that the coupon has been added but not yet applied due to certain conditions.

= What is Store Credit coupon? =
Store Credit is a form of virtual currency that can be used as a payment method for future purchases on our store. It acts like a gift card that retains its value until fully used.

= How do I use Store Credit for my purchases? =
During checkout, you can apply your Store Credit by entering the coupon code provided to you. The available balance will be deducted from your total order amount. Any remaining balance can be used for future purchases.

= Can customer use Store Credit partially? =
Yes, you can use Store Credit partially. If your Store Credit balance exceeds the total order amount, the remaining balance will stay on your account for future purchases.

= Can Store Credit expire? =
You can configure a expiry date in the coupon setting, after the expiry date the store credit will not be usable

= What happens to unused Store Credit if I return a purchase made with Store Credit? =
If you return a purchase made using Store Credit, the refunded amount will be issued back as Store Credit, which will be added to your existing Store Credit balance.

= Where can the customer check there Store Credit balance? =
If store credit was associated to email id, in that case the store credit coupon will be shown in the My account section of the user with that email id. In case store credit coupon was general and not linked to any email id, in that case the store credit coupon will be shown in the cart page when user applies the coupon

= How can I issue a refund as store credit? =
To issue a refund as store credit, navigate to the order edit page in your WooCommerce dashboard. From there, select the option to issue a refund. Choose whether you want to issue a full or partial refund and select the store credit option. Enter the amount to be refunded as store credit and confirm the refund.

= How does issuing a refund as store credit benefit my store? =
Issuing refunds as store credit encourages customers to make future purchases, helping you retain customer loyalty. It also simplifies the refund process and enhances the customer experience by providing instant credit for future use.

= Can store credit be used for any product in the store? =
Yes, store credit issued to customers can be used for any product available in your WooCommerce store. Customers can apply their store credit during checkout for their next purchase.

= Can customers request a cash refund instead of store credit? =
As the store administrator, you have the flexibility to choose whether to issue a refund as store credit or through other means such as cash or original payment method. Customers can request their preferred refund method, and you can decide based on your store policies.

= How customer will know they got store credit as refund? =
Customers will receive an email notification informing them that their refund has been issued as store credit. The email will include details of the store credit amount and the code.

= Can I send store credit email manually? =
Yes, you can send store credit email manually from the coupon edit page. Simply select the option to send store credit email, and the customer will receive an email notification with the store credit details.

= How to see the store credit of particular user =
* Admin can go to the coupon list page and there search coupon by email id (and select the coupon type as store credit), the store credit coupon will be shown in the list


== Changelog ==

= 1.1.62 =
* remove apply_coupon code from url if the coupon applied has auto add product in it , that way it wont keep adding product to cart on page refresh since url has changed and now it done have apply_coupon variable  

= 1.1.61 = 
* Tested for WC 9.3.3

= 1.1.60 =
* Tested for WC 9.3.0

= 1.1.49 =
* Tested for WC 9.2.3

= 1.1.47 =
* Tested for WC 9.2.0

= 1.1.44 =
* PHP 8.2 compatible

= 1.1.43 =
* fallback to fixed cart calculation
* Now you can find the store credit coupon by email id in the coupon list page

= 1.1.42 =
* Send store credit email manually

= 1.1.41 =
* our other plugins tab fixed

= 1.1.40 =
* Email send to the customer when they receive refund in the form of store credit coupon

= 1.1.39 =
* Issue refund as store credit directly from the order page

= 1.1.37 =
* Store Credit Coupon option given 

= 1.1.36 =
* Adding custom message for each coupon

= 1.1.34 =
* Payment Methods Restriction for Coupons
* User Role Restrictions for Coupons

= 1.1.33 =
* Exclude email id from using the coupon
* View orders where the coupon was been used
