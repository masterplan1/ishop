$(function(){
  const $addToCart = $('.btn-add-to-cart');
  const $cartQuantity = $('#cart-item-count');
  const $itemQuantity = $('.item-quantity');

  $addToCart.click(ev => {
    
    ev.preventDefault();
    const $this = $(ev.target);
    const id = $this.closest('.product-item').data('key');
    console.log(id);
    $.ajax({
      type: "POST",
      url: $this.attr("href"),
      data: {'id': id},
      success: function (response) {
        console.log($cartQuantity.text());
        $cartQuantity.text(parseInt($cartQuantity.text() || 0) + 1);
      }
    });
  });

  $itemQuantity.change(ev => {
    const $this = $(ev.target);
    const $tr = $this.closest('tr');
    const $td = $this.closest('td');
    const $id = $tr.data('id');
    const quantity = $this.val();
    $.ajax({
      type: "POST",
      url: $tr.data('url'),
      data: {'id': $id, 'quantity': quantity},
      success: function (data) {
        $td.next().text(data.price);
        $cartQuantity.text(data.quantity);
      }
    });
  });
});
