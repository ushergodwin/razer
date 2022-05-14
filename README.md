# Razer FRAMEWORK
- Razer is a PHP Framework that provides a convenient MVC (Model, View Controller) structure for systems development.
- Do you want something small but yet powerfull, this is your best choice
# Table of contents
## Database
1. [Insert](#insert-one--many)
2. [Update](#update-a-record)
3. [Delete](#delete-a-record)
4. [Truncate](#truncate-the-whole-table)
5. [Affected Rows](#affected-rows)
6. [Fetch Data](#fetch-all)
7. [Join Tables](#join-tables)
8. [Eloquent Models](#eloquent-models)
9. [Migrations](#migrations)
## HTTP
1. [Request](#requests)
2. [Response](#response)
3. [Redirect](#redirect)
4. [Routing](#routes)
## Insert one / Many
// For many, supply an array of arrays of data.
`DB::table('table_name')->save($data);`

## Last Insert Id
`DB::lastId();`

## Update a record
`DB::table('table_name')->where('id', 1)->update($data);`

## Delete a record
`DB::table('table_name')->where('id', 1)->delete();`

## Truncate the whole table
`DB::table('table_name')->delete();`

## Affected Rows
`DB::affectedRows();`
## Fetch all
`DB::table('table_name')->get();` // returns all columns

## Fetch specific columns
`$columns = 'column1, column2, column3 columnn'` //as a string

OR

`$columns = ['column1', 'column2', 'column3', 'columnn']` // as an array
`DB::table('table_name')->get($columns);`

## Fetch all columns with a condition
`DB::table('table_name')->where('id', 1)->get();`

## Fetch all columns with multiple conditions
// multiple call to the where method creates WHERE AND AND AND ...
// call orWhere to and an OR
`DB::table('table_name')->where('id', 1)
->where('age', 20 , '>')->where('gender', 'Male')->get();`

## Fetch one row with a condition
`DB::table('table_name')->row()->where('id', 1)->get();`

OR

`DB::table('table_name')->find(1);` // default column name is 'id'

## Fetch one value
`DB::table('table_name')->where('id', 1)->value();`

## Count number of rows
`DB::table('table_name')->where('id', 1)->count();`
`DB::table('table_name')->count();` // all rows without a condition

## Max / Min / Average value
`DB::table('table_name')->where('id', 1)->max();`
`DB::table('table_name')->where('id', 1)->min();`
`DB::table('table_name')->where('id', 1)->avg();`

## Select distict values
`DB::table('table_name')->distinct()->get();` // supply columns if not all
// distinct with a condition
`DB::table('table_name')->distinct()->where('id', 1)->get();`

## Join tables
`DB::table('table_name')->join('table2', 'table1.primary', 'table2.foregin')->get();`

// call the join method multiple times to join mutliple tables using INNER JOIN.
Other options of join methods include
`leftJoin(), rightJoin,() unionJoin()`

## Select with a Between clause
`DB::table('table_name')->between('age', 20, 25)->get();`

## Select data for pagenation
`DB::table('table_name')->range(1, 25)->get();`

## Check if the record exists
`DB::table('table_name')->where('id', 1)->exits();` // retuns true if exists

## Check if the record does not exist
`DB::table('table_name')->where('id', 1)->doesNotExist();` // oposite of exist

## Use a different database before querying
`Database::switchTo('database_name');`
// start querying from here

## Use a different database when querying
`DB::table('table_name')->use('database_name', 'table')->get();`


# Eloquent Models
- You can extend the Model class to you have you model called on its corresponding table name.
- The Model name should be singular and the table name in plural form
- The Eloquent model will convert your model name from singular to plural before querying the model objects.
## Insert
`$interns = new Intern($data);
$interns->save();`
## Affected Rows
`$interns->affectedRows();`
## Last Insert Id
`$interns->lastId();`

## Magic assignment for inserting
`$interns = new Intern();
$interns->name = "Godwin";
$interns->age = 20;
$inters->save();`

## Update a model
`Intern::find(1)->update($data);`

## Delete a resource
`Intern::find(5)->delete();`

## Fecth all
`Intern::all();` // same as `DB::table('interns')->get();`

## Fetch all with a condition
`Intern::where('id', 1)->get();` // same `DB::table('interns')->where('id', 1)->get();`

## Fetch one
`Intern::find(1);`

## Join models
`Intern::with('course')->get();` // this will assume that the interns and courses table use the Id column as its primary key, forming INNER JOIN courses ON interns.course_id = courses.id

## Join using the query builder
`Interns::with('course')->join('supervisor', 'interns.supervisor_id', 'supervisor.id')->get();`

## Execute a custom query
`DB::query('SELETE * FROM interns WHERE age > ?')->bindings([20])->get();`

- Only queries with bindings are executed with the query method

# Migrations
--------------------------------------------------------------------------------------
Create Database:                    |   `php manage make:db` if the database name is not specified in the .env configurations, use `php manage make:db dbname`
--------------------------------------------------------------------------------------
Make Migration:                     |   `php manage make:migration create_migration_name` This will create a migrations file under database/migrations directory. (tables names should be in a plural form)
--------------------------------------------------------------------------------------
RunAll Migrations:                  |   `php manage migrate` This will run all migrations
---------------------------------------------------------------------------------------
Migration a specific file:          |   `php manage migrate --file=filename` This will run migations for a single file. (do not put the file extension)
---------------------------------------------------------------------------------------
Group Migrations into 1 sql file    |   `php manage migrate:group` All migration files will be grouped into one sql file
----------------------------------------------------------------------------------------
Run grouped migration:              |   `php mange migrate:group --run` This will run grouped migrations
----------------------------------------------------------------------------------------
Run Migartion modifications         |   `php manage migrate:modifiy` This will run migration modifications
-----------------------------------------------------------------------------------------
List Migrations:                    |   `php manage migrate:list` Lists all run migrations
-----------------------------------------------------------------------------------------
Drop Migrations:                    |   `php manage migrate:rollback` Rolls back migrations
-----------------------------------------------------------------------------------------
Drop and re-run migrations:         |   `php manage migrate:refresh` Rools back and re-runs migrations
------------------------------------------------------------------------------------------
Show Migration logs/errors:         |   `php manage migrate:log` Logs Migrations errors
------------------------------------------------------------------------------------------
Clear Migration logs/errors:        |   `php manage migrate:log --clear` Clears migrations errors
------------------------------------------------------------------------------------------

# Controllers and Models        
## Case; All Controllers and Models should use CamelCase and should be in singular form
------------------------------------------------------------------------------------------
Make Controller:                    |   `php manage make:controller ControllerName` 
- //in singular Will create a controller under app/Controller/
- The resource controller is created with methods, index, create, store, show, edit, update, and destroy
------------------------------------------------------------------------------------------
Make a Resource Controller:         |   `php manage make:controller ControllerName --resource` 
- // Creates a resource controller with CRUD methods
------------------------------------------------------------------------------------------
Make Model:                         |   `php manage make:model ModelName` 
- // in singular Creates a model under app/Models
------------------------------------------------------------------------------------------
Make Model and its migration:       |   php manage make:model -M ModelName 
- // in singular
------------------------------------------------------------------------------------------

# Template                     
------------------------------------------------------------------------------------------
Clear Cache:                        |   php manage cache:clear
------------------------------------------------------------------------------------------
- For more information or inquiries, please call 
- +256 754438448 OR 
- Email godwintumuhimbise96@gmail.com


# Requests
- All methods that receive data through an HTTP POST request should have a $request paramater
- ## HTTP POST
` public function saveUser(Request $request)
{
  $name = $request->post('name'); // get the value of name sent through an HTTP POST
  echo $name;
}`

// OR
` public function saveUser(Request $request)
  {
    $name = $request->name; //dynamically assigned properties the Request Class
    echo $name;
  }`

// OR
 `public function saveUser(Request $request)
{
  $name = $request->body->name; //dynamically assigned properties the Request Class
  echo $name;
}`
- ## HTTP GET
` public function saveUser(Request $request)
{
  $name = $request->get('name'); // get the value of name sent through an HTTP GET
  echo $name;
}`

// OR
`public function saveUser(Request $request)
{
  $name = $request->name;
  echo $name;
}`

// OR
 `public function saveUser(Request $request)
{
  $name = $request->params->name;
  echo $name;
}`

# Response
The response class has 2 methods, ie send and json. Send() send a plain text response while json send a json formated respeonse.
- Both methods have 2 parameters
1. status --> http status code. Supported status codes are 200,202,302, 400, 401, 402, 403, 404, 408, 422, 500, 502
2. Message --> text / json reponse to send. 
- ## HTTP Plain text response

`public function login(Request $request)
  {
    $email = $request->post('email');
    return response()->send(200, $email);
  }`
  
 - ## HTTP JSON response
 `public function login(Request $request)
  {
    $email = $request->post('email');
    return response()->json(200, $email); // can be received through the message property
  }`
  
 # Redirect
 `redirect('user/dashboard');`
 - Redirect back
 `redirect()->back();`
 # Routes
 - The Routes class has 6 methods, get, post, group, except, name. and resource
 - ## GET
 - The get and post methods takes 2 arguements, $url (the url to go to) and $callback, an array of controller name and its method
 - Simple get route `Route::get('user/profile', [UserController::class, 'userProfile']);`
 - ## POST
 - Simple get route `Route::post('user/profile', [UserController::class, 'userProfile']);`
 - ## GROUP
 - Takes in 2 arguments, $prefix (array), $callback (closure)
 - `Route::group(['prefix' => 'admin', function(){ Route::get('/dashboard', [AdminController::class, 'index']); });` in the template ` <a href='{{ url('admin/dashboard') }}'>Dashboard</a>
 - ## Resource
 - This Route method is used to create routes for resource controllers
 - `Route::resource('products', ProductController::class);` // products is the prefix of the route
 - The above creates the following routes
 - /products (GET)
 - /products (PUT)
 - /products/create (GET)
 - /products/product_id (GET)
 - /products/product_id/edit (GET)
 - /products/product_id (DELETE)
 - /products/store (POST)
 - ## Except
 - You can call the except method to ignore the specified class methods when creating routes `Route::resource('products', ProductController::class)->except(['destroy']);`
 - ## name
 - On top of get and post methods, you can call the name method and register a short route name to use. This name must be used within the route method
 - In routes `Route::post('user/profile', [UserController::class, 'userProfile'])->name('user.p');`
 - In the template ` <a href='{{ route('user.p') }}'>Dashboard</a>` // takes you to user/profile
## End
