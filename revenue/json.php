<script id="scriptInit" type="text/javascript">
        $(document).ready(function () { 
            var netflx = "tabletest.php"; 
  
            $.ajax({ 
                dataType: "jsonp", 
                url: netflx, 
                jsonp: "$callback", 
                success: callback 
            }); 
        }); 
  
        function callback(result) { 
            // unwrap result 
            var names = []; 
            var prices = []; 
  
            var products = result["d"]; 
  
            for (var i = 0; i < products.length; i++) { 
  
                names.push(products[i].Product_Name); 
                prices.push(parseFloat(products[i].Unit_Price)); 
            } 
  
            $("#wijlinechartDefault").wijlinechart({ 
                axis: { 
                    y: { 
                        text: "Prices"
                          
                    }, 
                    x: { 
                        text: "Products"
                    } 
                }, 
                hint: { 
                    content: function () { 
                        return this.data.label + '\n ' + this.y + ''; 
                    } 
                }, 
                header: { 
                    text: "Top 10 Products by Unit Price - Northwind OData"
                }, 
                seriesList: [ 
                    { 
                        label: "Prices", 
                        legendEntry: true, 
                        fitType: "spline", 
                        data: { 
                            x: names, 
                            y: prices 
                        }, 
                        markers: { 
                            visible: true, 
                            type: "circle"
                        } 
                    } 
                ] 
            }); 
        } 
  
         
    </script>
  
  
    <div class="container"> 
        <div class="header"> 
            <h2> 
                External Data</h2> 
        </div> 
        <div class="main demo"> 
            <!-- Begin demo markup -->
            <div id="wijlinechartDefault" style="width: 756px; height: 475px" class="ui-widget ui-widget-content ui-corner-all"> 
            </div> 
            <!-- End demo markup -->
            <div class="demo-options"> 
                <!-- Begin options markup -->
                <!-- End options markup -->
            </div> 
        </div> 
        <div class="footer demo-description"> 
            <p> 
                This sample illustrates how to create a chart using data from an external data source. In this example, we are using data from the Netflix OData feed. 
            </p> 
            <ul> 
                <li>Data URL: <a href="http://demo.componentone.com/aspnet/Northwind/northwind.svc/Products?$format=json&$top=10&$orderby=Unit_Price%20desc">http://demo.componentone.com/aspnet/Northwind/northwind.svc/Products?$format=json&$top=10&$orderby=Unit_Price%20desc</a> </li> 
                <li>Documentation: <a href="http://www.odata.org/docs/">http://www.odata.org/docs/</a> </li> 
            </ul> 
        </div> 
    </div>
© 2012 GrapeCity, Inc. All Rights Reserved. All product and company names herein may be trademarks of their respective owners.