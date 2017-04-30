## [Unreleased]
### Added
- Container class for services and parameters
- Application core class
- Tests PSR-4 autoload
- Tests shortcut command
- Services and parameters configuration files
- Module main class with specific configuration file
- Unit of work pattern
- Entity Manager
- Routing component
- Templating component
- Daemon server to handle the game in real-time
- Event and listeners for system and sector seizing
- Realtime action scheduler
- Cyclic action scheduler
- Process manager
- Task manager
- Load balancer for processes

### Changed
- Many managers are now free of the session pattern
- Sector and system seizing program

### Removed
- Support of HHVM
- Support of PHP 5.5

## [2.0.0] - 2016-11-01
### Added
* Composer project file
* PHPUnit configuration file
* PSR-4 Autoload
* Travis CI configuration file
* Scrutinizer CI configuration file
* Editor configuration file
* NPM and Gulp configuratoin file

### Changed
* Classes are loaded by namespaces instead of manual loading
* Scripts keys dots replaced by underscores

### Removed
* Manual include of the modules
* Modules location constants
