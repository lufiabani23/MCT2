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
            $('#botaoNovoAgendamento #DataAgendamento').val(moment(start).format('YYYY-MM-DD'));
            $('#botaoNovoAgendamento #HoraAgendamento').val(moment(start).format('H'));
            $('#botaoNovoAgendamento #MinutoAgendamento').val(moment(start).format('m'));
            $('#botaoNovoAgendamento').modal('show');
        },
        eventRender: function(event, element) {
            element.bind('click', function() {
                $('#botaoEditarAgendamento #idEvento').val(event.id);
                $('#botaoEditarAgendamento #NomePaciente').val(event.title);
                $('#botaoEditarAgendamento #MotivoAgendamento').val(event.motivo);
                $('#botaoEditarAgendamento #OBSAgendamento').val(event.obs);
                $('#botaoEditarAgendamento #ValorAgendamento').val(event.valor);
                $('#botaoEditarAgendamento #DataAgendamento').val(moment(event.start).format('YYYY-MM-DD'));
                $('#botaoEditarAgendamento #HoraAgendamento').val(moment(event.start).format('H'));
                $('#botaoEditarAgendamento #MinutoAgendamento').val(moment(event.start).format('m'));
                $('#botaoEditarAgendamento').modal('show');
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
                motivo: '<?php echo $event['Motivo']; ?>',
                obs: '<?php echo $event['OBS.']; ?>',
                valor: '<?php echo $event['Valor']; ?>',
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
