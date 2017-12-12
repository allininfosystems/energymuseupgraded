<link rel="stylesheet" type="text/css" href="skin/frontend/default/default/css/styles.css" media="all">
<link rel="stylesheet" type="text/css" href="skin/frontend/default/default/css/energymuse_styles.css" media="all">
<table width="95%" border="0" cellspacing="15" cellpadding="15">
  <tr>
    <td>

 <form action="http://www.energymuse.com/store/index.php/review/product/post/id/<?php echo $_GET['prodid']; ?>" method="post" id="review-form">
    <div class="form-add">
      <h2>How do you rate this product? <em class="required">*</em></h2>
            <span id="input-message-box"></span>
            <table class="data-table" id="product-review-table">
                <col>
                <col width="1">
                <col width="1">
                <col width="1">
                <col width="1">
                <col width="1">
                <thead>
                    <tr class="first last">
                        <th>&nbsp;</th>
                        <th><span class="nobr">1 star</span></th>
                        <th><span class="nobr">2 stars</span></th>
                        <th><span class="nobr">3 stars</span></th>
                        <th><span class="nobr">4 stars</span></th>
                        <th><span class="nobr">5 stars</span></th>
                    </tr>
                </thead>
                <tbody>
                                    <tr class="first last odd">
                        <th><font style="font-size:12px">Rate this product</font></th>
                                            <td class="value"><input name="ratings[2]" id="Rate this product_1" value="6" class="radio" type="radio"></td>
                                            <td class="value"><input name="ratings[2]" id="Rate this product_2" value="7" class="radio" type="radio"></td>
                                            <td class="value"><input name="ratings[2]" id="Rate this product_3" value="8" class="radio" type="radio"></td>
                                            <td class="value"><input name="ratings[2]" id="Rate this product_4" value="9" class="radio" type="radio"></td>
                                            <td class="value last"><input name="ratings[2]" id="Rate this product_5" value="10" class="radio" type="radio"></td>
                  </tr>
              </tbody>
      </table>
            <input name="validate_rating" class="validate-rating" value="" type="hidden"><br/>
      <script type="text/javascript">decorateTable('product-review-table')</script>
                <ul class="form-list">
            <li>
              <label for="nickname_field" class="required">&nbsp;<em>*</em>Nickname</label>
                <div class="input-box">
                    <input name="nickname" id="nickname_field" class="input-text required-entry" type="text">
                </div>
            </li>
            <li>
              <label for="summary_field" class="required">&nbsp;<em>*</em>Summary of Your Review</label>
                <div class="input-box">
                    <input name="title" id="summary_field" class="input-text required-entry" type="text">
                </div>
            </li>
            <li>
              <label for="review_field" class="required">&nbsp;<em>*</em>Review</label>
                <div class="input-box">
                  <textarea name="detail" id="review_field" cols="5" rows="3" class="required-entry"></textarea>
                </div>
            </li>
        </ul>
   </div>
        <div class="buttons-set">
            <button type="submit" title="Submit Review" class="button"><span><span>Submit Review</span></span></button>
        </div>
</form>

</td>
  </tr>
</table>
<script type="text/javascript">
//<![CDATA[
    var dataForm = new VarienForm('review-form');
    Validation.addAllThese(
    [
           ['validate-rating', 'Please select one of each of the ratings above', function(v) {
                var trs = $('product-review-table').select('tr');
                var inputs;
                var error = 1;

                for( var j=0; j < trs.length; j++ ) {
                    var tr = trs[j];
                    if( j > 0 ) {
                        inputs = tr.select('input');

                        for( i in inputs ) {
                            if( inputs[i].checked == true ) {
                                error = 0;
                            }
                        }

                        if( error == 1 ) {
                            return false;
                        } else {
                            error = 1;
                        }
                    }
                }
                return true;
            }]
    ]
    );
//]]>
</script>


