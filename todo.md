\*\*\*\*# Todo

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
-   [x] form for users to add alternative
-   [x] have a fallback image for company/person if no image available
-   [x] only approve data on users pages
-   [x] about page / contact page
-   [x] sqlite wal mode and other stuff from https://github.com/nunomaduro/laravel-optimize-database
-   [x] inform users that this information are scraped we need their help to improve it
-   [x] Similar Sites

## Important

-   TheBSD

    -   [ ] remove any field/table that was not used at all for all of these imported data (until tech aviv\*\*\*\*)
    -   [ ] add indexes for approved_at fields
    -   [ ] site logo
    -   [ ] change App name
    -   [ ] remove unused comments
    -   [ ] publish this to server
    -   [ ] test it with laravel friends and others
    -   [ ] make backup like pinkary.com did and backup the images
    -   [ ] add backup to data every day
    -   [ ] return simple pagination instead of javascript one

-   Hamza
    -   [ ] people cards (getFirstMediaUrl) in javascript
    -   [ ] show person image in person details
    -   [ ] the whole cards is clickable
    -   [ ] investor details page
-   Data
    -   [ ] add data from https://www.israelitechalternatives.com/category/all/
    -   [ ] add data from https://github.com/ourcmcc/il-orgs
    -   [ ] data from https://stripealternatives.com/
    -   [ ] data from https://github.com/TechForPalestine/boycott-israeli-tech-companies-dataset especially alternatives
    -   [ ] data from https://www.usisrael.co/unicorn-tracker
    -   [ ] data from https://finder.startupnationcentral.org/

## Nice to have

-   TheBSD

    -   [ ] add images/avatars to cloudflare R2
    -   [ ] activity log in the filament dashboard
    -   [ ] add a blog (we can either use Statmic or take wave demo blog or prezet package)
    -   [ ] make the whole data translatable using spatie translatable and \_\_()
    -   [ ] make the site with roles: admin, publisher/content reviewer,
    -   [ ] add volunteers to add and update data
    -   [ ] add caching for data to make it faster
    -   [ ] office location lat lng https://www.latlong.net/
    -   [ ] add ads using https://www.madvert.co/
    -   [ ] in monitoring use scoutapm, or sentry.io, or rollbar, papertrail, or larabug, or datadoghq or sentry
    -   [ ] Analytics from google anayltics, Yandex, bing analytics, simpleanaylitecs
            or www.tinybird.co , https://simplestats.io/
    -   [ ] add to google search, bing search,
    -   [ ] use https://extract.pics/projects for image extraction easily
    -   [ ] status page like https://instatus.com/
    -   [ ] for feature requests https://www.featurebase.app/
    -   [ ] make automated tests for the whole site
    -   [ ] make readme file fot steps to run the project in your machine
    -   [ ] fix all tests then fix them with mutations
    -   [ ] add roles: admin (can change everything), editor (change everything except user management), user can only read data
    -   [ ] add simple authorization system for admin, editor, and user
    -   [ ] add inviting system using filamentphp [link](https://filamentapps.dev/blog/filament-invite-only-registration-via-email-invitations)
    -   [ ] change username and email git history for to theBSD

-   Hamza
    -   [ ] tags page
    -   [ ] form for users to add company
    -   [ ] Alternatives page
-   Abdu
    -   [ ] add proper design from Abdu
