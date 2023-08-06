# POROVNÁNÍ ZMĚNY ÚZEMÍ HOLEŠICE

 Aplikace vznikla v rámci studia na Přírodovědecké fakultě Univerzity Karlovy.

## Název diplomové práce - česky
Návrh aplikace pro vizualizaci geografických dat dokumentujících vývoj zaniklých krajin

## Název diplomové práce - anglicky
Design of an application for the visualization of geographic data documenting the development of defunct landscapes

## Zadání diplomové práce
Diplomová práce se bude zabývat přípravou geografických dat včetně návrhu jejich uložení do prostorové databáze a následnou 3D vizualizaci.
Cílem práce je vytvořit 3D webovou aplikaci pro vizualizaci dat dokumentujících vývoj využití krajiny v lokalitě Mostecka s využitím leteckých měřických snímků, DMR 4G a výstupů z projektu NAKI Zaniklé krajiny (shp). Aplikace bude umožňovat zobrazení dat reprezentující změny nadmořských výšek ve vybraných časových horizontech, které budou doplněny o příslušné typy krajinného pokryvu území.
Stěžejní částí práce bude co nejoptimálněji navrhnout serverovou (prostorová databáze, mapový server) a klientskou část aplikace.

Řešeno bude:
- prostorové ztotožnění vstupních sad,
- navržení prostorového dotazu pro co nejefektivnější převod bodové datové sady na polygonový model porovnávající nadmořskou výšku v různých časových horizontech. Zohledněny budou rovněž různé typy krajinného pokryvu.

Výstupem bude funkční webová aplikace zobrazující vytvořené polygony nad digitálním modelem terénu. Uvedené bude doplněno vrstvou typů krajinného pokryvu území v daném období. Aplikace bude využita k provedení vizuálního porovnání nadmořských výšek mezi jednotlivými časovými horizonty.
Celá aplikace bude zohledňovat požadavky vizualizace v prostředí internetu dat z databáze ve vhodném formátu v závislosti na vybrané technologii.
Hlavním přínosem práce bude návrh a tvorba prostorového dotazu se zakomponovanými časovými a prostorovými atributy a návrh komplexní architektury aplikace pro vizualizaci v prostředí internetu.

## Abstrakt
Tato diplomová práce se zabývá návrhem a následnou tvorbou 3D mapové aplikace pro
vizualizaci geografických dat dokumentujících vývoj krajiny v katastrálním území Holešice.
Přidanou hodnotou je tvorba různých variant prostorových SQL dotazů pro reprezentaci
změn výškové členitosti a využití území v podobě polygonů. V první části jsou náležitě
popsány dostupné technologie pro tvorbu aplikace společně se základními algoritmy
umožňující tvorbu polygonů. Praktická část začíná přípravou datové sady bodů vzniklou
datovou fúzí reprezentující výškovou členitost, která byla s ostatními daty uložena
v prostorové databázi. Následuje tvorba samotné aplikace na základě navržené architektury
fungující na principu klient-server. Principem je uložení výstupu některého z dotazů
v podobě materializovaného pohledu, ze kterého je v technologii GeoServeru vytvořena
mapová služba zobrazená v knihovně CesiumJS. Konkrétní volba výstupu a výběr varianty
dotazu závisí na odeslání formuláře ze strany uživatele.
### Klíčová slova: 
prostorová databáze, 3D vizualizace, mapová aplikace, zaniklé krajiny,
Mostecko, web


## Abstract
This thesis deals with the design and subsequent creation of a 3D map application for
visualization of geographic data documenting the development of the landscape in the
cadastral area of Holešice. The added value is the creation of different variants of spatial
SQL queries for the representation of changes in elevation and land use in the form of
polygons. In the first part, the available technologies for the creation of the application are
properly described together with the basic algorithms enabling the creation of polygons. The
practical part starts with the preparation of a dataset of points created by data fusion
representing the height division, which was stored with other data in a spatial database. This
is followed by the creation of the application itself based on the proposed client-server
architecture. The principle is to store the output of one of the queries in the form of
a materialized view. Map service based on the query is created in GeoServer technology and
displayed in the CesiumJS library. The specific choice of the output and the selection of the
query variant depends on the submission of the form by the user.

### Key words: 
spatial database, 3D visualization, map application, defunct landscapes, Most
region, web

