{if isset($footer_description) && !empty($footer_description)}
    <div class="flex justify-between gap-10">
        <div id="footer_description" class="article max-w-prose text-sm">
            {$footer_description}
        </div>
        {if $footer_image}
            <div class="flex-grow max-w-[600px] max-h-[600px] bg-contain bg-right-top bg-no-repeat bg-center -my-8 -mr-8" style="background-image: url({$footer_image});"></div>
        {/if}
    </div>
{/if}