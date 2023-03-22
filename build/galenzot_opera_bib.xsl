<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.1"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  
  xmlns:bib="http://purl.org/net/biblio#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:dcterms="http://purl.org/dc/terms/"
  xmlns:foaf="http://xmlns.com/foaf/0.1/"
  xmlns:link="http://purl.org/rss/1.0/modules/link/"
  xmlns:prism="http://prismstandard.org/namespaces/1.2/basic/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:z="http://www.zotero.org/namespaces/export#"
  
  xmlns="http://www.w3.org/1999/xhtml"
  exclude-result-prefixes="bib dc dcterms foaf link prism rdf z" 
  >
  <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes"/>
  <xsl:include href="galenzot_html.xsl"/>

  <!-- 
    
    <z:Collection rdf:about="opera">
        <dc:title>Galeni et Pseudo-Galeni opera</dc:title>
        <dcterms:hasPart rdf:resource="https://galenus-verbatim.huma-num.fr/tlg0530.tlg012"/>
   -->


  <xsl:template match="/">
    <div>
      <xsl:for-each select="/*/bib:Book[@rdf:about = $opera_ids]">
        <!-- Galenus first -->
        <xsl:sort select=".//foaf:surname"/>
        <xsl:sort select="dc:subject/dcterms:LCC/rdf:value"/>
        <xsl:apply-templates select="." mode="opus"/>
      </xsl:for-each>
    </div>
  </xsl:template>
  
  
</xsl:transform>