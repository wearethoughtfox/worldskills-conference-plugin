=== Worldskills Conference ===

1. Create plugin 
`npx @wordpress/create-block worldskills-conference --namespace=worldskills --variant=dynamic`

2. Change version in package.json 
`"@wordpress/scripts": "^27.8.0"`
Run `npm install`
See: https://github.com/WordPress/gutenberg/issues/62202

Notes 

## This plugin 
1. Registers custom taxonomy - conference 
2. Registers custom post types for sessions and speakers 
3. Adds two custom blocks: 
    1. Get speakers - primarily for use on sessions page to get speakers by custom taxomony (shared by the session)
    2. Get sessions - primarily for use on speakers page to get sessions by custom taxomony (shared by the speakers)

## To install the plugin
1. Download from github
2. Open Zip
3. Rename to worldskills-conference
4. Zip up
5. Upload plugin 
6. Plugin already exists - continue 
7. Select the zip file again 
8. Replace current with uploaded 

## Session data
- Excerpt 
- Session type 
- Session tags 
- Session location 

### Custom fields 
- event_date - 2024-09-12
- event_time - 11:00
- event_time_end - 12:00
- track - track-1   

## Speaker data
- Title: Name 
- Excerpt: Title 
- Session name tags: connect to session 
- Body: bio
- Speaker image: image 

## Backgrounds 
- ws-background-wings
- ws-background-trumpet
- ws-background-spiral

## Speaker sorting 
This sorting method provides a flexible way to prioritize certain speakers (via boost), de-prioritize others (via reduce), and maintain an alphabetical order for the rest.

- All speakers with custom field speaker_boost will come first, sorted from highest boost to lowest.
- Then, speakers without boost or reduce will be sorted by custom field last_name.
- Finally, speakers with speaker_reduce will be sorted from lowest reduce to highest.

## Using custom fields in query loops 
- Use the is_moderator custom field to add an extra element to show they are a moderator 
- Surprisingly difficult (at least to me!)
- Used a plugin https://metafieldblock.com/how-it-works/
- Then use a filter to transform the 'true' to the moderator element