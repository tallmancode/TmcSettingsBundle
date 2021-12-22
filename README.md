# Tallmancode Settings Bundle
A solution for user / application settings in a Symfony project.

## Usage

Create a class and extend AbstractTmcSettings also add the TmcSettingsResource annotation

    @TmcSettingsResource(relationClass = "App/Entity/SomeEntity", settingsGroup="someSettings")

if the settings group is related to another entity add the fully qualified class name of that entity to the relationClass option. 
