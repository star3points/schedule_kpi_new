<template>
  Worker
  <div>
    <a-card class="card" :body-style="{padding: '.6em'}">
      <a-row>
        <a-col :span="13">
          <div style="transform: scale(.85)">
            <div class="header-card">{{shopName}}</div>
            <div class="header-card">{{workerData.name_user}}</div>
            <div class="header-card">{{workerData.position_user}}</div>
          </div>
        </a-col>
        <a-col :span="11">
          <div class="header-card">План</div>
          <div class="large-digit">{{numberWithSpaces(workerData.plan_sum)}}</div>
        </a-col>
      </a-row>
    </a-card>

    <a-card class="card">
      <a-row>
        <a-col :span="12">
          <div class="header-card">Выполнение</div>
          <div class="ring-container">
            <div class="ring">
            </div>
            <div class="ring-overlay"
                 v-bind:style="{
                      transform: 'rotate(' + (45 + (Number(workerData.fact_percent) * (180/130))) + 'deg)'}">
            </div>
            <span class="percent-label">
                {{Number(workerData.fact_percent).toFixed(1)}}%
                </span>
          </div>
        </a-col>

        <a-col :span="12">
          <div class="header-card">Прогноз</div>
          <div class="ring-container">
            <div class="ring">
            </div>
            <div class="ring-overlay"
                 v-bind:style="{
                      transform: 'rotate(' + (45 + (Number(workerData.forecast_percent) * (180/130))) + 'deg)'}">
            </div>
            <span class="percent-label">
                {{Number(workerData.forecast_percent).toFixed(1)}}%
                </span>
          </div>
        </a-col>
      </a-row>
    </a-card>

    <a-card class="card">
      <a-row>
        <a-col :span="8">
          <div class="header-card">
            Всего часов
          </div>
          <div class="large-digit">
            {{workerData.plan_hours}}
          </div>
        </a-col>
        <a-col :span="8">
          <div class="header-card">
            Часов отработано
          </div>
          <div class="large-digit">
            {{workerData.worked_hours}}
          </div>
        </a-col>
        <a-col :span="8">
          <div class="header-card">
            Часов осталось
          </div>
          <div class="large-digit">
            {{(workerData.plan_hours - workerData.worked_hours).toFixed(1)}}
          </div>
        </a-col>
      </a-row>
    </a-card>

    <a-card class="card" :body-style="{padding: '.8em'}">
      <a-row>
        <a-col :span="6">
          <div class="header-card">
            Чеки
          </div>
          <div class="large-digit">
            {{workerData.checks_units}}
          </div>
        </a-col>
        <a-col :span="6">
          <div class="header-card">
            Товаров
          </div>
          <div class="large-digit">
            {{workerData.products_units}}
          </div>
        </a-col>
        <a-col :span="6">
          <div class="header-card">
            UPT
          </div>
          <div class="large-digit">
            {{workerData.UPT}}
          </div>
        </a-col>
        <a-col :span="6">
          <div class="header-card">
            Средний чек
          </div>
          <div class="large-digit">
            {{Number(workerData.check_average).toFixed()}}
          </div>
        </a-col>
      </a-row>
    </a-card>

    <a-card class="card">
      <a-row>
        <a-col :span="12">
          <div class="header-card">Сумма возвратов</div>
          <div class="large-digit">
            {{Math.abs(workerData.return_sum)}}
          </div>
        </a-col>
        <a-col :span="12">
          <div class="header-card">Доля от продаж</div>
          <div class="large-digit">
            {{ (Math.abs(workerData.return_sum / workerData.fact_sum) * 100).toFixed(1)}} %
          </div>
        </a-col>
      </a-row>
    </a-card>

    <a-card class="card">
      <a-row>
        <a-col :span="12">
          <div class="header-card">Количество возвратов</div>
          <div class="large-digit">
            {{Math.abs(workerData.return_products)}}
          </div>
        </a-col>
        <a-col :span="12">
          <div class="header-card">Доля по количеству</div>
          <div class="large-digit">
            {{ (Math.abs(workerData.return_products / workerData.products_units) * 100).toFixed(1)}} %
          </div>
        </a-col>
      </a-row>
    </a-card>
  </div>
</template>

<script>
export default {
  name: "Worker",
  props: ['workerData']
}
</script>

<style scoped>

</style>