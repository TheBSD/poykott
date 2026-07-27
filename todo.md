# Todo

## Data

- [x] fix the github action for server when push
- [x] add robots.txt and llm.txt to the exclude security rule in cloudflare

- [x] add data from crunch base <!-- importer exists; production import status not verified -->
- [x] get Crunchbase competitors from `database/seeders/data/13-crunchbase.json` <!-- scraper and test exist; generated output not present -->
- [ ] make it multi-language for static and dynamic data using AI
  - [ ] use the most seven spoken languages in the world
  - [ ] then the most seven spoken languages for the Islamic countries

## Important

- [x] improve design <!-- substantial design work exists; no completion criteria for the remaining polish -->
- [ ] add patchhog.dev to security scan each commit

## Nice to have

- [ ] switch to pnpm or bun if faster
- [x] activity log in the filament dashboard
- [ ] upgrade tailwind to v4 for performance boost
- [ ] security from https://app.aikido.dev/
- [ ] add a blog (we can either use Statmic or take wave demo blog or prezet package)
- [ ] make the whole data translatable using spatie translatable and <!-- no translation implementation found -->
- [ ] make the site with roles: admin, publisher/content reviewer, <!-- roles exist, but these exact roles/workflows are not verified -->
- [ ] add volunteers to add and update data
- [ ] add caching for data to make it faster <!-- only targeted filter/geocoding caches found -->
- [x] office location lat lng https://www.latlong.net/ <!-- fields and geocoding action exist; data coverage not verified -->
- [ ] add ads using https://www.madvert.co/ (not working anymore)
- [ ] add to google search console, bing search,
- [ ] use https://extract.pics/projects for image extraction e easily
- [ ] status page like https://instatus.com/
- [ ] for feature requests https://www.featurebase.app/
- [ ] make automated tests for the whole site
- [x] make readme file fot steps to run the project in your machine
- [ ] fix all tests then fix them with mutations
- [x] add roles: admin (can change everything), editor (change everything except user management), user can <!-- Shield roles/policies exist; exact permission matrix not verified -->
      only
      read data
- [x] add simple authorization system for admin, editor, and user
- [ ] add inviting system using
      filamentphp [link](https://filamentapps.dev/blog/filament-invite-only-registration-via-email-invitations)
- [ ] change username and email git history for to theBSD
- [ ] return simple pagination instead of javascript one
- [ ] make backup like pinkary.com did and backup the images
- [ ] remove unused comments
- [ ] tags page
- [x] form for users to add company
- [x] Alternatives page
