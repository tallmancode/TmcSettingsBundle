# Tallmancode Settings Bundle
An application settings bundle for Symfony 5+ projects, allowing flexable settings with relationships and intergration with Api Platform. This bundle allows you to store and use settings through out an application without hassle.

### Installation
The bundle can be installed using Composer or the [Symfony binary](https://symfony.com/download):
``` composer require tallmancode/settings-bundle ```
Once the bundle has installed create the settings table migration and run the migration

``` php bin/console make:migration ```

```php bin/console doctrine:migrations:migrate ```

Full documentaion available at [TMC Settings Bundle Docs](https://tallmancode.co.za/docs/settings-bundle)