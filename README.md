## dbog
Database object generator project AKA 'dbog' is a php library developed 
for synchronizing MySql databases. Can be used on multiple database instances 
to synchronize them either by common structure definition or by custom 
definitions for each database instance.

### Usage
Run via CLI script.
```
./dbog

Parameters
----------
--output-queries                           - executed SQL queries are logged if specified (OPTIONAL)
--dry-run                                  - SQL queries are NOT executed if specified (OPTIONAL)
--verbose                                  - more detailed log output if specified (OPTIONAL)
--help                                     - shows this help 

```

### Set structure definition
See `demo/*` for structure definition example.

Create `table` or `view` definition classes, that must extend `\Src\Core\Table` or `\Src\Core\View` classes.
Create `schema` class extending `Src\Core\Schema` and register all schema's tables and views.

### Main configuration
See `conf/ConfigExample.php` for more information.
Create `Conf\Config` class and set DB connection configuration and main configuration for database intances.

### Changelog

- 2018-10-07 - first syncer draft.
  - tables - create, drop, rename
  - columns - add, drop, rename, support of necessary datatypes, null columns, default values etc.
  - keys - PK, UQ, FK, IX - add, remove. Synchronize automatically after column change.
  - triggers - create, drop
 - 2018-10-08 - Implement views support. 
 - 2018-10-09 - Added structure definition validator before database sync.
 - 2018-10-15 - Fixed synchronization of all FKs that leads on changed PK.
 