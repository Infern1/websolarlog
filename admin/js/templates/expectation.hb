<div style="height:300px;">
    <form>
        <input type="hidden" name="s" value="save_expectation" />
        <input type="hidden" name="id" value="{{data.inverterId}}" />
        What is the predicted kWh a year:<br>                        
        <input name="totalProdKWH" class="monthProd" id="totalKWHProd" value=""><br><br>
        <div id="expectation">    
            <div class="expectation_row">
                <div class="expectation_cell monthName">Months</div>
                {{#data.month}}    
                    <div id="month{{this}}" class="expectation_cell  monthName">{{this}}</div>
                {{/data.month}}
            </div>    
    
            <div class="expectation_row">
                <div class="expectation_cell  monthProd">kWh</div>
                {{#data.month}}    
                    <div class="expectation_cell  monthProd"><input id="{{this}}KWH" value=""></div>
                {{/data.month}}    
            </div>
            
            <div class="expectation_row">
                <div class="expectation_cell monthProd">%</div>
                {{#data.month}}
                <div class="expectation_cell  monthProd"><input class="monthProd" name="{{this}}PER" id="{{this}}PER" readonly="true" value=""></div>
                {{/data.month}}
            </div>
        </div>
        <div style="clear: both" />
        <button type="button" id="btnExpectationSubmit">save</button>
    </form>              
    <br><br>
    <div id="expectation_graph">
    <span>Graphs</span>
    <div style="clear: both" />
    {{#data.month}}
    <p class="monthBAR"><img class="monthIMG" id="{{this}}BAR" src=""/></p>
    {{/data.month}}            
    <div style="clear: both" />
    {{#data.month}}
    <p class="monthBAR_label">{{this}}</p>
    {{/data.month}}            
</div>