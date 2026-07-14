Translations completed using AI may not be accurate. Please create a pull request to correct mistakes or contact us at digitalhumanities@csuohio.edu.

## Regenerating translation files

Regenerate the template with **both** keywords:

```bash
xgettext --language=PHP --from-code=utf-8 --keyword=__ --keyword=plural:1,2 \
    --no-wrap --output=languages/template.base.pot $(find . -name "*.php" -not -path "./.git/*" | sort)
```

Then update each catalog and recompile:

```bash
cd languages
for lang in de_DE es_ES fr_FR; do
    msgmerge --backup=off --update $lang.po template.base.pot
    msgfmt --check --statistics -o $lang.mo $lang.po
done
```

### IMPORTANT: plural strings must remain `msgid_plural` entries

Strings used via `plural('singular', 'plurals', $n)` (currently: item/items,
image/images, audio file/audio files, video/videos, Factoid/Factoids) must be
stored as gettext plural entries (`msgid` + `msgid_plural` + `msgstr[0]`/`msgstr[1]`),
never as separate plain entries. The `--keyword=plural:1,2` flag above produces
the correct form.

Why: Omeka's `plural()` routes through Zend_Translate's plural lookup, which
finds the *singular* msgid and indexes the stored value with the plural rule
(0 or 1). Plural entries store an array, so this works. A plain string entry
gets indexed as a PHP string offset instead — e.g. a plain `msgid "item"` /
`msgstr "Objekt"` entry makes the admin dashboard stats render `'Objekt'[1]`,
i.e. the single letter "b", and because plugin translations override core's,
it breaks core UI too. This bug has shipped twice (fixed in 8005843 and again
in July 2026); don't reintroduce it.

Note also that the `Plural-Forms` header differs per language: `(n != 1)` for
de_DE/es_ES, `(n > 1)` for fr_FR.
