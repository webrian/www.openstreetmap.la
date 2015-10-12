<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>

<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
    <ShortName>OpenStreetMap.la Places</ShortName>
    <Description>Search places in Laos using OpenStreetMap.la</Description>
    <Tags>openstreetmap places laos</Tags>
    <Url type="application/rss+xml"
         template="<?php echo $this->Url->build("/places/rss?q={searchTerms}&p={startPage?}", true); ?>"/>
    <Url type="text/html"
         template="<?php echo $this->Url->build("/?q={searchTerms}", true); ?>"/>
    <Url type="application/x-suggestions+json"
         template="<?php echo $this->Url->build("/places/suggest?q={searchTerms}&p={startPage?}", true); ?>"/>
    <LongName>OpenStreetMap.la places search</LongName>
    <Image height="16" width="16" type="image/vnd.microsoft.icon"><?php echo $this->Url->build("/favicon.ico", true); ?></Image>
    <Query role="example" searchTerms="Phone" />
    <Attribution>Data Copyright by OpenStreetMap contributors</Attribution>
    <Language>en-us</Language>
    <OutputEncoding>UTF-8</OutputEncoding>
    <InputEncoding>UTF-8</InputEncoding>
</OpenSearchDescription>
