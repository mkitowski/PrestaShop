{if isset($export_customers_form)}
    {$export_customers_form}
{/if}

{if isset($export_customers_list)}
    {$export_customers_list}
{/if}

{if isset($campaign_days)}
    <script>
        (function ($) {
            var available_cycles = $.parseJSON('{$campaign_days}');

            var cycles1 = cycles.init(
                available_cycles,
                $('#campaign'),
                $('#autoresponder_day'),
                $('#addToCycle_1'),
                {$cycle_day}
            );
        })(jQuery);
    </script>
{/if}