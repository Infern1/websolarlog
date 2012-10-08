<div style="height:300px;">
    <form>
        <input type="hidden" name="s" value="save_expectation" />
        <input type="hidden" name="id" value="{{data.inverterId}}" />
        What is the predicted kWh a year:<br>                        
        <input name="totalProdKWH" class="monthProd" id="totalKWHProd" value=""><br><br>
        <div id="expectation">    
            <div class="expectation_row">
                <div class="expectation_cell monthName">Months</div>
                {{#data.month_perc}}    
                    <div id="month{{this.month}}" class="expectation_cell  monthName">{{this.month}}</div>
                {{/data.month_perc}}
            </div>    
    
            <div class="expectation_row">
                <div class="expectation_cell  monthProd">kWh</div>
                {{#data.month_perc}}    
                    <div class="expectation_cell  monthProd"><input id="{{this.month}}KWH" value=""></div>
                {{/data.month_perc}}    
            </div>
            
            <div class="expectation_row">
                <div class="expectation_cell monthProd">%</div>
                {{#data.month_perc}}
                <div class="expectation_cell  monthProd"><input class="monthProd" name="{{this.month}}PER" id="{{this.month}}PER" readonly="true" value="{{this.perc}}"></div>
                {{/data.month_perc}}
            </div>
        </div>
        <div style="clear: both" />
        <button type="button" id="btnExpectationSubmit">save</button>
        <button type="button" id="btnExpectationDefault">default</button>
    </form>              
    <br><br>
    <div id="expectation_graph">
    <span>Graphs</span>
    <div style="clear: both" />
    {{#data.month_perc}}
    <p class="monthBAR"><img class="monthIMG" id="{{this.month}}BAR" src=""/></p>
    {{/data.month_perc}}            
    <div style="clear: both" />
    {{#data.month_perc}}
    <p class="monthBAR_label">{{this.month}}</p>
    {{/data.month_perc}}            
</div>