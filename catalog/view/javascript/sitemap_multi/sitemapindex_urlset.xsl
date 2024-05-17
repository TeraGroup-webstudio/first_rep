<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
				xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
				xmlns:xhtml="http://www.w3.org/1999/xhtml">
	<xsl:output method="html" indent="yes" encoding="UTF-8"/>
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta charset="UTF-8"/>
			<title>XML Sitemap Index</title>
			<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/sitemap_multi.min.css"/>
		</head>
		<body>
		<div class="container">
			<h1>XML Sitemap Index</h1>
			<p class="description">
				Sitemap counter: <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/>
			</p>
			<xsl:apply-templates/>
		</div>
		</body>
		</html>
	</xsl:template>
	<xsl:template match="sitemap:sitemapindex">
		<table>
			<thead>
			<tr>
				<th class="center" width="1%">#</th>
				<th width="75%">Sitemap</th>
			</tr>
			</thead>
			<tbody>
			<xsl:for-each select="sitemap:sitemap">
				<xsl:variable name="sitemapURL">
					<xsl:value-of select="sitemap:loc"/>
				</xsl:variable>
				<tr>
					<td>
						<xsl:value-of select="position()"/>
					</td>
					<td>
						<div>
							<a href="{$sitemapURL}">
								<xsl:value-of select="sitemap:loc"/>
							</a>
						</div>
					</td>
				</tr>
			</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>
</xsl:stylesheet>