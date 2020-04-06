# Jonathan's E-commerce API 

This API is made for a school assignment task:<br>
```
Make an API for an e-commerce platform.
You should be able to manage, fetch , and products.
You should also be able to manage users.
```

## Prerequisities

This API is made with PHP and MariaDB (mySQL).<br>
I've used XAMPP while developing this API to run a local server with these prerequisities.

## Install

I've included a databasedump with test data:
```
database.sql
```

Import this sql-script into your prefered administration tool.

## Test data - Users

There are two registrered users: One admin and one regular user.<br>
These two users has different roles and rights. You can read more about this in the endpoints section.

Admin has the rights to manage products, users, shoppingcart and purchases etc:
```
Username: Admin
Password: password
Email: Admin@myCompany.com
```

Users can only get their own data regarding their shopping experience.

```
Username: User
Password: password
Email: User@myCompany.com
```

## Coding standard

Class names: MUST be singular form and ```PascalCase```.<br>
Methods and functions: ```camelcase```.<br>
Instances of a class and SQLqueries: ```snake_case``` and end with ```_handler``` example: ```$user_handler = new User```.<br>
Parameters in functions and methods: ```camelCase``` and MUST end with ```_IN``` example: ```isUsernameTaken($username_IN)```.<br>

Every other name should be in camelCase if nothing else is set.

## End points v1

### Tokens
Every end point except for ```login_User``` needs an active token to work.
Token gets invalid if user hasn't done any actions the last 15mins.<br>
User has to login again to get a new token.

### Users
* [add_User](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#add_User)
* [login_User](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#login_User)
### Products
* [create_Product](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#create_Product)
* [delete_Product](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#delete_Product)
* [get_Product](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#get_Product)
* [getAll_Products](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#getAll_Products)
* [sort_Products](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#sort_Products)
* [update_Product](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#update_Product)
### Carts
* [addTo_Cart](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#addTo_Cart)
* [checkout_Cart](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#checkout_Cart)
* [empty_Cart](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#empty_Cart)
* [get_Cart](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#get_Cart)
* [getAll_Carts](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#getAll_Carts)
* [removeFrom_Cart](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#removeFrom_Cart)
### Purchases
* [get_Purchase](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#get_Purchase)
* [getAll_Purchases](https://github.com/Tacohontas/Jonathan_Individuell-uppgift---API/blob/master/README.md#getAll_Purchases)
* **SortPurchases** : will be added in v2
### Stock
* **Will get added in v2**

## add_User
Insert user to DB if:

- No field is empty
- Username isnt taken 
- Email isnt taken

You're also able to set user roles based on table in DB.
A message will be returned on success.

## login_User
Login user to DB if:

- No field is empty
- User exists in DB

A token will be created and then returned on success.

## create_Product
Create product and add it to DB if:

- User is admin
- No field is empty
- Token is valid

Returns:
- Confirm message with Product Name on success
- error messages on failed operations

## delete_Product
Deletes product from DB (and from shopping carts) if:

- User is admin
- No field is empty
- Token is valid
- Product exists

Returns:
- Confirm message on success
- Error message/s on failed operations

## get_Product
Get product/s from DB based on a combination of column and value

Column = which column to match with value<br>
Value  = which value match with column<br>

Example:
```getProduct(Color, "Yellow")``` will return a product/s with color yellow. 

## getAll_Products

Get all products!<br>
You need to set an limit and offset for pagination causes.

Returns:
- Result on success
- Error message on failed operations or faulty inputs.

## sort_Products

Get & sort products!<br>
You need to set an limit and offset for pagination causes.

Returns:
- Result on success
- Error message on failed operations or faulty inputs.

## update_Product
Update product if:
- product exist.
- User is admin.
- Token is valid.

Returns:
- a confirm message on success.
- error messages on failed operations.

## addTo_Cart
Add product to user's shopping cart if:

- No field is empty
- Token is valid

Will get A confirm message on success.<br>
Error message/s on failed operations.

## checkout_Cart
Checkout cart and add to purchase table in DB if:

- No field is empty
- Token is valid
- Cart is valid and has products in it. (Cart automatically deletes if it's empty)

Will get an overview over purchase details on success.<br>
Error message/s on failed operations

## empty_Cart
Empties the cart by cartId if you're admin, or your own cart if you're not admin.

Requirements:
- Token is valid
- Cart exists

Returns 
- confirm message on success
- Error message/s on failed operations

## get_Cart
Get cart:<br>
Admin can get any cart. Cart id is required<br>
Regular users can only get their own cart by token. Not by Cart Id.

Returns
- Cart with products and total on success
- Error messages on failed operations

## getAll_Carts
Get all active carts (not the finished ones) if:
- Token is valid
- User is admin

Returns
- Active carts on success
- Error messages on failed operations

## removeFrom_Cart
Removes product from shopping cart.<br>
If cart doesnt exist                        = returns error message<br>
If cart is empty after product is removed   = delete cart<br>
If product is removed and cart is not empty = Returns a confirmation message.<br>

## get_Purchase
Get purchase by purchase Id. <br>
Users can only get their own purchases.<br>
Admins can get any purchase by puchase id.<br>

- Need valid token
- No empty values is allowed

If purchase doesnt exist = return error message<br>
If purchase exist        = return purchase

## getAll_Purchases
Get all the purchases  <br>
Users can only get their own purchases  <br>
Admins can get any purchase by puchase id. <br> 

- Need valid token
- No empty values is allowed

Returns:
- All purchases on success
- FALSE if there is none
- Error messages on failed operations

