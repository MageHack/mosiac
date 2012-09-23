# Mosaic

Re-thinking how we browse product images. Thinking about device size variation, especially table browzing.

<img src="http://f.cl.ly/items/11003I0P1l3i2Q0x1l3y/mosaic.png" width="800" />
<img src="http://f.cl.ly/items/163o1P0B2a3C0a1i0n0w/mosaic-flip.png" width="800" />
<img src="http://f.cl.ly/items/0G003H3J063V2w1r2d3e/mosaic-filter.png" width="800" />

# Features

    - Mosaic block gets sales information to rank products as bestselling, moderate selling and low selling
    - This information is used to change the size of product tiles.  Larger tiles are products that have sold more often
    - Click a tile to find more info.
    - Click the info icon to get short description and add to basket.
    - AJAX loading of more products
    - Layered navigation filters
    - A whole lotta awesomeness.

# Todo

    - Improve loading of sales data with product collection.  Currently very hacky as we couldn't get a left join on the product collection and didn't want to filter our products that have been bought.
    - There's a hack in there for page size. 
    - Actually add a product to basket.
