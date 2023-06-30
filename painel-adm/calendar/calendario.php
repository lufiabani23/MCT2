

<script>
function modalShow() {
    $('#modalShow').modal('show');
}

$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listYear'
        },
        locale: 'pt-br',
        defaultDate: '<?php echo date('Y-m-d'); ?>',
        editable: true,
        navLinks: true,
        eventLimit: true,
        selectable: true,
        selectHelper: true,
        select: function(start, end) {
            $('#ModalAdd #inicio').val(moment(start).format('DD-MM-YYYY HH:mm:ss'));
            $('#ModalAdd #termino').val(moment(end).format('DD-MM-YYYY HH:mm:ss'));
            $('#ModalAdd').modal('show');
        },
        eventRender: function(event, element) {
            element.bind('click', function() {
                $('#ModalEdit #id_evento').val(event.id);
                $('#ModalEdit #titulo').val(event.title);
                $('#ModalEdit #descricao').val(event.description);
                $('#ModalEdit #inicio').val(moment(event.start).format('DD-MM-YYYY HH:mm:ss'));
                $('#ModalEdit #termino').val(moment(event.end).format('DD-MM-YYYY HH:mm:ss'));
                $('#ModalEdit').modal('show');
            });
        },
        eventDrop: function(event, delta, revertFunc) {
            edit(event);
        },
        eventResize: function(event, delta, revertFunc) {
            edit(event);
        },
        events: [
            <?php foreach($events as $event):
            $start = explode(" ", $event['Data_Inicio']);
            $end = explode(" ", $event['Data_Fim']);
            if($start[1] == '00:00:00'){
                $start = $start[0];
            }else{
                $start = $event['Data_Inicio'];
            }
            if($end[1] == '00:00:00'){
                $end = $end[0];
            }else{
                $end = $event['Data_Fim'];
            }
            ?>
            {
                id: '<?php echo $event['ID']; ?>',
                title: '<?php echo $event['Paciente']; ?>',
                description: '<?php echo $event['Motivo']; ?>',
                start: '<?php echo $start; ?>',
                end: '<?php echo $end; ?>',
            },
            <?php endforeach; ?>
        ]
    });

    // Verificar se está em um celular e definir o modo "Compromissos"
    if (window.matchMedia("(max-width: 768px)").matches) {
        $('#calendar').fullCalendar('changeView', 'listYear');
    }

    function edit(event) {
        var start = moment(event.start).format('DD-MM-YYYY HH:mm:ss');
        var end = moment(event.end).format('DD-MM-YYYY HH:mm:ss');
        var id_evento = event.id;

        var Event = [];
        Event[0] = id_evento;
        Event[1] = start;
        Event[2] = end;

        $.ajax({
            url: 'evento/action/eventoEditData.php',
            type: "POST",
            data: { Event: Event },
            success: function(rep) {
                if (rep == 'OK') {
                    alert('Modificação Salva!');
                } else {
                    alert('Falha ao salvar, tente novamente!');
                }
            }
        });
    }
});

</script>
