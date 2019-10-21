[app]
app.versao="{$tag.versao}"
app.url="{$tag.url}"

[gama]
gama.versao={$ver.versao}
gama.url={$ver.url}

[php]
{foreach from=$lista item=item key=key}
{$key}={$item}
{/foreach}

[apache]
{foreach from=$listaApache item=item key=key}
mod.{$key}={$item}
{/foreach}