/* Pop up window capability */
var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
  if(popUpWin)
  {
    if(!popUpWin.closed) popUpWin.close();
  }
  popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbar=no,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}

/* Simple JS print */
function printPage(arg) { print(document); }

/*Print as you please, sort of */
function printPageD(orientation) {
        if (typeof(customPrintService) == "undefined") {
                document.write("You need to install the Extension to print this.");
        }
        else {
                var printOptions = {"print_footerleft":"", "print_footercenter":"", "print_footerright":"", "print_headerleft":"",  "print_headercenter":"",  "print_headerright":"", "print_in_color":false, "use_native_print_dialog":false, "show_print_progress":false};
                printOptions["print_orientation"] = orientation;

                customPrintService.printDocument(printOptions);
        }
}


/* This is the doLoad for shiko_skedine. */
/*
<!--
    Use the "onload" event to start the refresh process.
-->
<body onload="doLoad()">
*/
var sURL = unescape(window.location.pathname);

function doLoad_SS()
{
    // the timeout value should be the same as in the "refresh" meta-tag
    setTimeout( "refresh()", 2*1000 );
}

function refresh()
{
    //  This version of the refresh function will be invoked
    //  for browsers that support JavaScript version 1.2
    //

    //  The argument to the location.reload function determines
    //  if the browser should retrieve the document from the
    //  web-server.  In our example all we need to do is cause
    //  the JavaScript block in the document body to be
    //  re-evaluated.  If we needed to pull the document from
    //  the web-server again (such as where the document contents
    //  change dynamically) we would pass the argument as 'true'.
    //
    window.location.reload( true );
}

/* Use with body load */

function second_field_focus(formname, fieldname, fieldname2) {

/*     if (document.formname.fieldname.length > 4) {
          alert(document.formname.fieldname.value);
          document.formname.fieldname2.focus();
     }
*/

     if (document.getElementById(fieldname).length > 4) {
          alert('nooo');
//          alert(document.getElementById(fieldname).value);
     }
}
