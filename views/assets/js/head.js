fncSweetAlert = (type, icon, text, time) => {

  const response = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    showCloseButton: false,
    timer: time,
    timerProgressBar: true,
  })

  switch (type) {

    case "confirm":

    return new Promise(resolve=>{ 

      Swal.fire({
        icon: icon,
        text: text,
        customClass: "swal-question",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Confirmar',
      }).then(function(result){

        resolve(result.value);

      });

    });

    break;

    case "notify":

    response.fire({
      icon: icon,
      title: text
    });

    break;

    case "notify-reload":

    response.fire({
      icon: icon,
      title: text
    }).then((result) => {
      if (result.dismiss === Swal.DismissReason.timer) {
        location.reload();
      }
    });

    break;

  }  
  
}