{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2016 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<table id="dashbypaymentPanel" class="table table-responsive data_table">
  <thead>
    <tr>
      <th class="text-center">{l s='Payment method' mod='dashbypayment'}</th>
      <th class="text-center">{l s='Number of transactions' mod='dashbypayment'}</th>
      <th class="text-center">{l s='Generated turnover' mod='dashbypayment'}</th>
      <th class="text-center">{l s='Average shopping cart' mod='dashbypayment'}</th>
      <th class="text-center">{l s='Percentage of transactions' mod='dashbypayment'}</th>
      <th class="text-center">{l s='Percentage of turnover' mod='dashbypayment'}</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$dashbypaymentBestPaymentMethod item=paymentMethod}
    <tr>
      <td class="text-center">{$paymentMethod.paymentMethod|escape:'htmlall':'UTF-8'}</td>
      <td class="text-center">{$paymentMethod.nbTransac|escape:'htmlall':'UTF-8'}</td>
      <td class="text-center">{$paymentMethod.generatedCA|escape:'htmlall':'UTF-8'}</td>
      <td class="text-center">{$paymentMethod.AverageBasket|escape:'htmlall':'UTF-8'}</td>
      <td class="text-center">{$paymentMethod.percentTransac|escape:'htmlall':'UTF-8'}</td>
      <td class="text-center">{$paymentMethod.percentCA|escape:'htmlall':'UTF-8'}</td>
    </tr>
    {/foreach}
  </tbody>
</table>
