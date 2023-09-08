---
title: Preset Properties
description: All pages receive some properties from the database by default. You can add custom properties in the Admin interface.
tags: [getting-started]
layout: page
---
# Preset Properties
All Pages receive some properties from the database by default. Please use the Vue Devtools to inspect the proper formatting of these properties.
They are usually in the same format as the corresponding database table, but sometimes they may contain additional data.

| Property       | Description                                  |
|----------------|----------------------------------------------|
| announcements  | All announcements                            |
| artworks       | All artworks                                 |
| pages          | All pages, sorted by `sort`                  |
| schedule       | All schedule entries, ordered_by `starts_at` |
| appScreen      | The current screen data                      |
| appScreen.room | The primary room of that screen.             |
| rooms          | All **active** rooms for that screen         |

## Custom Properties
You can add custom properties in the Admin interface.
1. Go to the Admin interface
2. Click on `Pages` in the sidebar
3. Click on `Edit` for the page you want to add properties to
4. Click on `Add Property`
5. Fill in the form and click on `Save`
6. Repeat steps 4 and 5 for all properties you want to add
7. Now go to the `Playlists` tab
8. Click on `Edit` for the playlist you want to add properties to
9. Make sure you selected the correct `Page` in the dropdown of the Playlist Item
10. Your custom properties can be set under `Edit` for the Playlist Item
