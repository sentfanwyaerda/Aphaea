weather.alias
=============
``weather.alias`` is a JSON-file providing meta-data of each image in the icon pack. The most important functionality is identifying the type of weather it depicts. 

```
[
{"code": 100000, "src": "sunshine.png", "tags": ["sun"]}
]
```

###Weather code
Describing weather is a complex process. Multiple variables define the <u>visual expression</u>: cloudiness, during day or night, the amount of rain, snow or hail, and all kinds of extra's.
**Pattern:** ``[1-3][0-3]{4}[0-9a-f]`` or ``[0](000-360)(0,1,3)[c]`` (in case of 'compass' files)

####Athmospheric body
- 1: Sun &ndash;
- 2: Moon &ndash;
- 3: Cloudy / Unknown / Not visible &ndash;

####Level of cloudiness and amount of rain, snow and/or hail
- 0: None
- 1: Minimal
- 2: Medium
- 3: Heavy
*note:* The weather code is based upon the image names of the 'weather' icon-pack. While its '3' value might been used to indicate 'minimal' levels ("now and then"), turning '1' into 'medium' and '2' into 'heavy'.

####With special markers
- 0: None
- 1: Lightning &ndash;
- 2: Warning &ndash; In most cases it will be specified as other special markers, like: storm (4), freezing / slippery (5), hot (b).
- 3: Fog &ndash;
- 4: Windy / Storm &ndash;
- 5: Freezing / Slippery &ndash;
- 6: Smog &ndash;
- 7: Eclips &ndash; Indicates an eclips of the sun, or an eclips of the moon. In combination with the 'athmospheric body' (first character: 1 or 2).
- 8: Hazy &ndash;
- 9: "chance for" &ndash;
- a: Rainbow &ndash;
- b: Hot / High temperatures &ndash;
- c: Compass &ndash;
- f: Logo / Other

###Weather tags
Some examples: *sun, moon, clouds; rain, snow, hail; thunder, fog, ice, flurry, sleet, overcast, hazy, hot, freezing, slippery, smog, windy, storm, rainbow, eclips; minimal, medium, maximal; compass*