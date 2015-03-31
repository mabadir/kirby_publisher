# Kirby Publisher

Kirby Publisher a PHP publishing script for Kirby.

## Introduction

Kirby Publisher is a PHP class that scans the Kirby content directory, and looks inside each article directory for the _blogarticle.txt_ file, if the file contains a specific YAML variable _published_ with value **Publish**, Kirby Publisher will ensure the article is published (i.e. precedes the directory name with a number). On the other hand, if the variable value is **Draft** the article directory is drafted (i.e. the number preceding the article name is removed if exists).

## Features

The initial version hosts some basic features to streamline the process:

1. Automatic article numbering; Kirby Publisher will detect the latest article ID used, and assign an incremented ID to the next published article.
2. Reserve IDs across articles, once an ID is reserved to an article, it will not be assigned to another article.
3. Configurable YAML variables, if you are already using a similar YAML variable, no need to configure a new one.
4. Backward compatible with old posts, even if the YAML variable is not defined for old articles, it can still process new ones.

## Configuration

In order to run Kirby Publisher, a helper script is developed on PHP as well.
Kirby Publisher can be used within any application, it required the following configuration parameters:

> $CONTENT\_PATH // This is the Kirby content directory, where the articles are hosted.
> $TOOLKIT\_PATH // This is the Kirby toolkit path.
> $DIRECTORY\_DIGITS = 4 // This is the number of digits used in directory names, for example, 0012-kirby-publisher (Default = 4).
> $YAML\_DIRECTIVE="published" //This is the YAML variable name to be used (Default = published)
> $PUBLIHSED\_VALUE="Publish" //This is the value Kirby Publisher will check to Publish the article (Default = Publish).
> $DRAFT\_VALUE="Draft" // This is the value Kirby Publisher will check to Draft the article (Default = Draft).

In order to run Kirby Publisher, from any PHP script initialize an object passing all the configuration parameters above.

> $kirby\_publisher = new kirby_publisher($CONTENT_PATH,$TOOLKIT\_PATH,$DIRECTORY\_DIGITS,$YAML\_DIRECTIVE,$PUBLISHED\_VALUE,$DRAFT\_VALUE);

Only the first two parameters are mandatory, the rest are optional, and the object will be initialized with their default values.

## Run Kirby Publisher
In order to do the magic, just call the **update\_published\_status()** method.

> $kirby\_publisher-\>update\_published\_status(); 