# Todo

## Done

-   [x] scaffold using blueprint
-   [x] test the tests!
-   [x] add proper data & structure from https://github.com/TheBSD/scraping-israeli-data/ using custom commands
    -   [x] unicorn-graduate
    -   [x] unicorn
    -   [x] team
    -   [x] members
    -   [x] profiles
    -   [x] jobs/job companies
    -   [x] the resources (morph) for person and companies in each command
    -   [x] fix resource types and titles
    -   [x] fix OfficeLocation relation to be many to many
    -   [x] Add Approved at for every resource Added from Commands
-   [x] Filament Admin panel
    -   [x] proper data and structure
    -   [x] add relationship for many-to-many and has many
    -   [x] taggable morph many relationship in admin
    -   [x] OfficeLocation many to many -> companies
    -   [x] generate filament admin panel from existing models
-   [x] make it open source on https://github.com/TheBSD/
-   [x] paid for the domain
-   [x] use https://github.com/tighten/duster in project/actions/husky
-   [x] import all images for companies and people
-   [x] Use Spatie media library to optimize images
-   [x] spatie media filament
-   [x] get image optimized, then the original one
-   [x] slug instead of id for companies/investors/people ..etc

## Important

-   TheBSD

    -   [ ] have a fallback image for company/person/alternative if no image available
    -   [ ] remove any field/table that was not used at all for all of these imported data (until tech aviv\*\*\*\*)
    -   [ ] publish this to server
    -   [ ] test it with laravel friends and others
    -   [ ] change username and email git history for to theBSD
    -   [ ] make backup like pinkary.com did and backup the images
    -   [ ] sqlite wal mode and other stuff from https://github.com/nunomaduro/laravel-optimize-database
    -   [ ] add backup to data every day
    -   [ ] about page
    -   [ ] friends sites
    -   [ ] inform users that this information are scraped we need their help to improve it

-   Hamza
    -   [ ] people cards (getFirstMediaUrl) in javascript
    -   [ ] show person image in person details
    -   [ ] the whole cards is clickable
    -   [ ] investor details page
    -   [ ] form for users to add alternative
    -   [ ] form for users to add company

## Nice to have

-   TheBSD

    -   [ ] add images/avatars to cloudflare R2
    -   [ ] add a blog (we can either use Statmic or take wave demo blog or prezet package)
    -   [ ] make the whole data translatable using spatie translatable and \_\_()
    -   [ ] make the site with roles: admin, publisher/content reviewer,
    -   [ ] add volunteers to add and update data
    -   [ ] add caching for data to make it faster
    -   [ ] office location lat lng https://www.latlong.net/
    -   [ ] scrape finder.startupnation.com data and imported here
    -   [ ] add ads using https://www.madvert.co/
    -   [ ] in monitoring use scoutapm, or sentry.io, or rollbar, papertrail, or larabug, or datadoghq or sentry
    -   [ ] Analytics from google anayltics, Yandex, bing analytics, simpleanaylitecs or www.tinybird.co
    -   [ ] add to google search, bing search,
    -   [ ] use https://extract.pics/projects for image extraction easily
    -   [ ] status page like https://instatus.com/
    -   [ ] for feature requests https://www.featurebase.app/
    -   [ ] make automated tests for the whole site
    -   [ ] make readme file fot steps to run the project in your machine

-   Hamza
    -   [ ] tags page
    -   [ ] Alternatives page
-   Abdu
    -   [ ] add proper design from Abdu
