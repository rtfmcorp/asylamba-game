## [2.1.3] - 2017-08-11
### Added
- Messages when a ruler keeps his role due to election without candidates
- Handling of elections without candidates when the previous chief is dead

### Changed
- Inactive accounts in dev mode are now displayed

### Fixed
- Elections without candidates (empty messages and cycle blocking)
- Empty places resources value out of range
- Ranking diff

### Removed
- Some recurrent user error logging

## [2.1.2] - 2017-08-09
### Added
- Storage of the sectors ownership data in Redis

### Fixed
- Commanders defection when their base is taken
- Display of the incoming attacks in the admiralty
- Sectors ownership data in faction tactical registry
- Ending of recycling missions
- Conversion of the recycling places when there is no resources left

## [2.1.1] - 2017-07-23
### Added
- Spy and combat active report highlighting in the list
- Daily scheduling for notifications cleaning and inactive accounts management
- Daily scheduling for rankings
- Set an INI parameter for max execution time
- Redis session handling

### Fixed
- External invitations for registration
- School experience for commanders
- Access to admin interface
- Empty investments
- Conversation with no recipients error message
- Storage building informations
- Flashbags for queue's ending notification
- Ship recycling
- Parrainage reward
- Commercial shipping travel time
- Capital leaving
- Defenders losses after a successful defense
- Workers access to the session data

## [2.1.0] - 2017-06-01
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
