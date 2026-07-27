# Cloudflare Rules


## Security Rules
- [x] Add `robots.txt` to the exclude security rule in Cloudflare 
- [ ] add `llm.txt` to the exclude security rule in Cloudflare (should be added to cloudflare platform and routes then here as documentation)

```
(
    http.host in {
        "israelitechalternatives.com"
        "www.israelitechalternatives.com"
    }
    and not (
        http.request.uri.path in {
        "/"
        "/about"
        "/alternatives"
        "/companies"
        "/companies/create"
        "/contact"
        "/faqs"
        "/newsletter"
        "/similar-sites"
        "/opcache"
        "/up"
        "/webhooks/mailchimp"
        "/.well-known/security.txt"
        "/robots.txt"
        "/admin"
        }
        or http.request.uri.path wildcard "/admin/*"
        or http.request.uri.path wildcard "/alternative/*"
        or http.request.uri.path wildcard "/company/*"
        or http.request.uri.path wildcard "/companies/*"
        or http.request.uri.path wildcard "/investors/*"
        or http.request.uri.path wildcard "/people/*"
        or http.request.uri.path wildcard "/filament/*"
        or http.request.uri.path wildcard "/livewire-*/*"
        or http.request.uri.path wildcard "/storage/*"
        or http.request.uri.path wildcard "/build/*"
        or http.request.uri.path wildcard "/css/*"
        or http.request.uri.path wildcard "/js/*"
        or http.request.uri.path wildcard "/fonts/*"
        or http.request.uri.path wildcard "/images/*"
        or http.request.uri.path wildcard "/favicon.ico"
    )
)
```
