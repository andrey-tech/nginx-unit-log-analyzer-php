# NGINX Unit log analyzer

![NGINX Unit log analyzer logo](./assets/nginx-unit-log-analyzer-logo.png)

[![Latest Stable Version](https://poser.pugx.org/andrey-tech/nginx-unit-log-analyzer-php/v)](https://packagist.org/packages/andrey-tech/nginx-unit-log-analyzer-php)
[![PHP Version Require](https://poser.pugx.org/andrey-tech/nginx-unit-log-analyzer-php/require/php)](https://packagist.org/packages/andrey-tech/nginx-unit-log-analyzer-php)
[![Total Downloads](https://poser.pugx.org/andrey-tech/nginx-unit-log-analyzer-php/downloads)](https://packagist.org/packages/andrey-tech/nginx-unit-log-analyzer-php)
[![License](https://poser.pugx.org/andrey-tech/nginx-unit-log-analyzer-php/license)](https://packagist.org/packages/andrey-tech/nginx-unit-log-analyzer-php)

[Russian version (русскоязычная версия)](#russian-version)

The NGINX Unit Log Analyzer is a utility for analyzing log files from the [NGINX Unit](https://unit.nginx.org/) application server.
It examines the execution durations of NGINX Unit processes for deployed applications with a precision of one second, generating various types of reports in either tabular or graphical formats.

## Contents

<!-- MarkdownTOC levels="1,2,3,4,5,6" autoanchor="true" autolink="true" -->

- [Installation](#installation)
- [Command Line Arguments and Options](#command-line-arguments-and-options)
  - [Command Line Options](#command-line-options)
- [Types of Generated Reports](#types-of-generated-reports)
  - [Report Type `day`](#report-type-day)
  - [Report Type `top`](#report-type-top)
  - [Report Type `graph`](#report-type-graph)
- [Authors](#authors)
- [License](#license)

<!-- /MarkdownTOC -->

<a id="installation"></a>
## Installation

The NGINX Unit Log Analyzer requires:

- [PHP](https://www.php.net/) version 8.1 or higher
- [Composer](https://getcomposer.org/)
- [GNU Plot](http://www.gnuplot.info/) version 5.4 or higher

To install via Composer:

```shell
composer require --dev andrey-tech/nginx-unit-log-analyzer-php
```

To install GNU Plot using [APT (Advanced Package Tool)](https://en.wikipedia.org/wiki/APT_(software)):

```shell
apt install gnuplot
```

<a id="command-line-arguments-and-options"></a>
## Command Line Arguments and Options

```shell
./vendor/bin/nginx-unit-log-analyzer [OPTIONS] <log_file>
```

Where:

[OPTIONS] are the command line options,  
<log_file> is the path to the NGINX Unit log file containing information about application processes, formatted as:

```log
2024/06/13 13:31:06 [info] 657#657 "application-3" application started
2024/06/13 13:32:14 [notice] 151#151 app process 657 exited with code 0
```

<a id="command-line-options"></a>
### Command Line Options


| Option                      | Description                             | Value                                                 | Default                         | Example Usage                                      |
|-----------------------------|-----------------------------------------|-------------------------------------------------------|---------------------------------|----------------------------------------------------|
| `--log-timezone`            | Log file timezone                       | Timezone name                                         | Script's timezone               | `--log-timezone Europe/Moscow`                     |
| `--report-type`             | Type of report to generate              | `day`, `top`, `graph`                                 | `graph`                         | `--report-type day`                                |
| `--report-timezone`         | Report timezone                         | Timezone name                                         | Script's timezone               | `--report-timezone Europe/Moscow`                  |
| `--filter-start-time-from`  | Filter log entries by start time (from) | Date and time string                                  | —                               | `--filter-start-time-from 2024-06-17 00:00:00 UTC` |
| `--filter-start-time-to`    | Filter log entries by start time (to)   | Date and time string                                  | —                               | `--filter-start-time-to 2024-06-17 23:59:59 UTC`   |
| `--filter-application-name` | Filter log entries by application name  | Application name                                      | —                               | `--filter-application-name application-1`          |
| `--graph-file-name`         | Filename for `graph` report type        | Filename                                              | `unit.png`                      | `--graph-file-name unit.png`                       |
| `--graph-types`             | Types of graphs for `graph` report      | `quantity`, `median`, `average`, `maximum`, `minimum` | `quantity`, `maximum`, `median` | `--graph-types quantity --graph-types median`      |
| `--no-color`                | Disable colored output in console       | —                                                     | —                               | `--no-color`                                       |


<a id="types-of-generated-reports"></a>
## Types of Generated Reports

The NGINX Unit Log Analyzer can generate three types of reports (the `--report-type` option):

1. `day` — Execution durations of NGINX Unit processes for deployed applications, averaged over one hour and grouped by day.
2. `top` — A top list of the longest and shortest NGINX Unit process executions for deployed applications, grouped by day.
3. `graph` (default) — Graphs of NGINX Unit process execution durations for deployed applications, averaged over one hour.

The `day` and `top` reports are generated in the console in tabular form.  

The `graph` report is generated as a graphical file in [PNG](https://en.wikipedia.org/wiki/PNG) format and **requires** the [GNU Plot](http://www.gnuplot.info/) utility for its creation.  

The `graph` report can include the following types of graphs (`--graph-types`):

* `quantity` (default) — A graph showing the number of NGINX Unit processes for deployed applications, averaged per hour.
* `average` (default) — A graph of the [arithmetic mean](https://en.wikipedia.org/wiki/Arithmetic_mean) execution duration of NGINX Unit processes for deployed applications, averaged per hour.
* `median` — A graph of the [median](https://en.wikipedia.org/wiki/Median) execution duration of NGINX Unit processes for deployed applications, averaged per hour.
* `maximum` — A graph of the maximum execution duration of NGINX Unit processes for deployed applications, averaged per hour.
* `minimum` — A graph of the minimum execution duration of NGINX Unit processes for deployed applications, averaged per hour.

<a id="report-type-day"></a>
### Report Type `day`

A sample day report fragment for one day:

![NGINX Unit log analyzer. Report type `day`](./assets/nginx-unit-log-analyzer-report-type-day.png)

In the report table:

- **`DATE`** — the analyzed date with timezone information;
- **`APP`** — a list of application names that were launched during the analyzed date;
- **`Processes`** — information about launched NGINX Unit processes:
  - **`Start`** — the analyzed time interval, hours (from-to);
  - **`Amount`** — the number of processes launched during the time interval (`+n` — number of unfinished processes);
- **`Duration`** — information about the execution duration of processes during the time interval:
  - **`Median`** — median value;
  - **`Average`** — arithmetic mean value;
  - **`Std dev`** — standard deviation of duration;
  - **`Min`** — minimum value;
  - **`Max`** — maximum value.

The duration values for the execution of NGINX Unit processes in the table are in the format:  
`d h m s`, where: `d` — days, `h` — hours, `m` — minutes, `s` — seconds.


<a id="report-type-top"></a>
### Report Type `top`

Example fragment of a `top` report for a single day:

![NGINX Unit log analyzer. Report type `top`](./assets/nginx-unit-log-analyzer-report-type-top.png)

In the report table:

- **`DATE`** — the analyzed date with timezone information;
- **`APP`** — a list of application names that were launched during the analyzed date;
- **`Duration`** — the duration of the process execution;
- **`App name`** — the application name;
- **`Start time`** — the date and time when the process was started;
- **`Exit time`** — the date and time when the process finished;
- **`Start`** — the line number in the NGINX Unit log file where the process start was recorded;
- **`End`** — the line number in the NGINX Unit log file where the process exit was recorded;
- **`Id`** — the [process identifier](https://en.wikipedia.org/wiki/Process_identifier) in NGINX Unit.

At the top of the table, there is a list of the 20 longest-running processes recorded during the analyzed date.

At the bottom of the table, there is a list of the 5 shortest-running processes recorded during the analyzed date.

The lists are sorted in descending order of duration.


<a id="report-type-graph"></a>
### Report Type `graph`

Example of a `graph` report, including 2 graphs:

- A graph showing the number of NGINX Unit processes for launched applications, averaged per hour;
- A graph showing the [median](https://en.wikipedia.org/wiki/Median) process execution duration.

![NGINX Unit log analyzer. Report type `graph`](./assets/nginx-unit-log-analyzer-logo.png)

The format of the process execution duration values on the graph is:
`H:MM:SS`, where: `H` — hours, `MM` — minutes, `SS` — seconds.

<a id="authors"></a>
## Authors

© 2024-2025 andrey-tech

<a id="license"></a>
## License

This library is distributed under the [MIT](./LICENSE) license.


-----------------------------------------------------------------------------------------------------------------------


<a id="russian-version"></a>
# NGINX Unit log analyzer

NGINX Unit log analyzer — это утилита для анализа лог-файлов сервера приложений [NGINX Unit](https://unit.nginx.org/).
Утилита анализирует продолжительность исполнения процессов NGINX Unit для запущенных приложений с точностью в одну секунду
и формирует в консоли отчёты различных типов в табличной или графической форме.

<a id="%D0%A1%D0%BE%D0%B4%D0%B5%D1%80%D0%B6%D0%B0%D0%BD%D0%B8%D0%B5"></a>
## Содержание

<!-- MarkdownTOC levels="1,2,3,4,5,6" autoanchor="true" autolink="true" -->

- [Установка](#%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0)
- [Аргументы и опции командной строки](#%D0%90%D1%80%D0%B3%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D1%8B-%D0%B8-%D0%BE%D0%BF%D1%86%D0%B8%D0%B8-%D0%BA%D0%BE%D0%BC%D0%B0%D0%BD%D0%B4%D0%BD%D0%BE%D0%B9-%D1%81%D1%82%D1%80%D0%BE%D0%BA%D0%B8)
    - [Опции командной строки](#%D0%9E%D0%BF%D1%86%D0%B8%D0%B8-%D0%BA%D0%BE%D0%BC%D0%B0%D0%BD%D0%B4%D0%BD%D0%BE%D0%B9-%D1%81%D1%82%D1%80%D0%BE%D0%BA%D0%B8)
- [Типы формируемых отчётов](#%D0%A2%D0%B8%D0%BF%D1%8B-%D1%84%D0%BE%D1%80%D0%BC%D0%B8%D1%80%D1%83%D0%B5%D0%BC%D1%8B%D1%85-%D0%BE%D1%82%D1%87%D1%91%D1%82%D0%BE%D0%B2)
    - [Отчёт типа `day`](#%D0%9E%D1%82%D1%87%D1%91%D1%82-%D1%82%D0%B8%D0%BF%D0%B0-day)
    - [Отчёт типа `top`](#%D0%9E%D1%82%D1%87%D1%91%D1%82-%D1%82%D0%B8%D0%BF%D0%B0-top)
    - [Отчёт типа `graph`](#%D0%9E%D1%82%D1%87%D1%91%D1%82-%D1%82%D0%B8%D0%BF%D0%B0-graph)
- [Авторы](#%D0%90%D0%B2%D1%82%D0%BE%D1%80%D1%8B)
- [Лицензия](#%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F)

<!-- /MarkdownTOC -->

<a id="%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0"></a>
## Установка

NGINX Unit log analyzer требует:
 * [PHP](https://www.php.net/) версии не ниже 8.1;
 * [Composer](https://getcomposer.org/);
 * [GNU Plot](http://www.gnuplot.info/) версии не ниже 5.4.

```shell
composer require --dev andrey-tech/nginx-unit-log-analyzer-php
```

Установка GNU Plot с помощью [APT (Advanced Package Tool)](https://en.wikipedia.org/wiki/APT_(software)):
```shell
apt install gnuplot
````

<a id="%D0%90%D1%80%D0%B3%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D1%8B-%D0%B8-%D0%BE%D0%BF%D1%86%D0%B8%D0%B8-%D0%BA%D0%BE%D0%BC%D0%B0%D0%BD%D0%B4%D0%BD%D0%BE%D0%B9-%D1%81%D1%82%D1%80%D0%BE%D0%BA%D0%B8"></a>
## Аргументы и опции командной строки

```shell
./vendor/bin/nginx-unit-log-analyzer <NGINX Unit log file> [OPTIONS]
```
где:

* `[OPTIONS]` — опции командной строки,
* `<NGINX Unit log file>` — путь к лог-файлу NGINX Unit, содержащему информацию о запущенных процессах приложений вида:


```log
2024/06/13 13:31:06 [info] 657#657 "application-3" application started
2024/06/13 13:32:14 [notice] 151#151 app process 657 exited with code 0
```

<a id="%D0%9E%D0%BF%D1%86%D0%B8%D0%B8-%D0%BA%D0%BE%D0%BC%D0%B0%D0%BD%D0%B4%D0%BD%D0%BE%D0%B9-%D1%81%D1%82%D1%80%D0%BE%D0%BA%D0%B8"></a>
### Опции командной строки

| Опция                       | Описание                                   | Значение                                              | По умолчанию                     | Пример использования                               |
|-----------------------------|--------------------------------------------|-------------------------------------------------------|----------------------------------|----------------------------------------------------|
| `--log-timezone`            | Часовой пояс лог-файла                     | Имя часового пояса                                    | Часовой пояс скрипта             | `--log-timezone Europe/Moscow`                     |
| `--report-type`             | Тип формируемого отчёта                    | `day`, `top`, `graph`                                 | `graph`                          | `--report-type day`                                |
| `--report-timezone`         | Часовой пояс отчёта                        | Имя часового пояса                                    | Часовой пояс скрипта             | `--report-timezone Europe/Moscow`                  |
| `--filter-start-time-from`  | Фильтр записей лога по времени, от         | Строка даты и времени                                 | —                                | `--filter-start-time-from 2024-06-17 00:00:00 UTC` |
| `--filter-start-time-to`    | Фильтр записей лога по времени, до         | Строка даты и времени                                 | —                                | `--filter-start-time-to 2024-06-17 23:59:59 UTC`   |
| `--filter-application-name` | Фильтр записей лога по приложению          | Имя приложения                                        | —                                | `--filter-application-name application-1`          |
| `--graph-file-name`         | Имя файла графиков для отчёта типа `graph` | Имя файла                                             | `<NGINX Unit log file>.png`      | `--graph-file-name unit.png`                       |
| `--graph-types`             | Типы графиков для отчёта `graph`           | `quantity`, `median`, `average`, `maximum`, `minimum` | `quantity`, `maximum` и `median` | `--graph-types quantity --graph-types median`      |
| `--no-color`                | Отключение цветов в консоли                | —                                                     | —                                | `--no-color`                                       |

<a id="%D0%A2%D0%B8%D0%BF%D1%8B-%D1%84%D0%BE%D1%80%D0%BC%D0%B8%D1%80%D1%83%D0%B5%D0%BC%D1%8B%D1%85-%D0%BE%D1%82%D1%87%D1%91%D1%82%D0%BE%D0%B2"></a>
## Типы формируемых отчётов

Утилита NGINX Unit log analyzer может формировать отчёты трех типов (`--report-type`):

1. `day` — продолжительность исполнения процессов NGINX Unit для запущенных приложений с усреднением за один час и разбиением по дням;
2. `top` — топ-лист наиболее и наименее продолжительных процессов NGINX Unit для запущенных приложений с разбиением по дням;
3. `graph` (по умолчанию) — графики продолжительности исполнения процессов NGINX Unit для запущенных приложений с усреднением за один час.

Отчёты типа `day` и `top` формируется в консоли в табличной форме.

Отчёт типа `graph` формируется в графическом файле формата [PNG](https://en.wikipedia.org/wiki/PNG)
и **требует** для своего создания утилиту [GNU Plot](http://www.gnuplot.info/).

Отчёт типа `graph` может включать следующие виды графиков (`--graph-types`):

* `quantity` (по умолчанию) — график количества процессов NGINX Unit для запущенных приложений с усреднением за один час;
* `average` (по умолчанию) — график [среднеарифметической](https://en.wikipedia.org/wiki/Arithmetic_mean) продолжительности исполнения процессов NGINX Unit для запущенных приложений с усреднением за один час;
* `median`  — график [медианной](https://en.wikipedia.org/wiki/Median) продолжительности исполнения процессов NGINX Unit для запущенных приложений с усреднением за один час;
* `maximum` — график максимальной продолжительности исполнения процессов NGINX Unit для запущенных приложений с усреднением за один час;
* `minimum` — график минимальной продолжительности исполнения процессов NGINX Unit для запущенных приложений с усреднением за один час.

<a id="%D0%9E%D1%82%D1%87%D1%91%D1%82-%D1%82%D0%B8%D0%BF%D0%B0-day"></a>
### Отчёт типа `day`

Пример фрагмента отчёта типа `day` для одного дня:

![NGINX Unit log analyzer. Report type `day`](./assets/nginx-unit-log-analyzer-report-type-day.png)

В таблице отчёта:

* `DATE` — анализируемая дата с указанием часового пояса;
* `APP` — список имён приложений, которые были запущены за анализируемую дату;
* `Processes` — информация о запущенных процессах NGINX Unit:
  - `Start` — анализируемый интервал времени, часы (от-до);
  - `Amount` — количество процессов, которые были запущены в течение интервала времени (`+n` — число незавершённых процессов);
* `Duration` — информация о продолжительности исполнения процессов в течение временного интервала:
  - `Median` — [медианное](https://en.wikipedia.org/wiki/Median) значение;
  - `Average` — [среднеарифметическое](https://en.wikipedia.org/wiki/Arithmetic_mean) значение;
  - `Std dev` — [среднеквадратическое отклонение](https://en.wikipedia.org/wiki/Standard_deviation) продолжительности;
  - `Min` — минимальное значение;
  - `Max` — максимальное значение.

Формат значений продолжительности исполнения процессов NGINX Unit в таблице имеет вид:
`d h m s`, где: `d` — дни, `h` — часы, `m` — минуты, `s` — секунды.

<a id="%D0%9E%D1%82%D1%87%D1%91%D1%82-%D1%82%D0%B8%D0%BF%D0%B0-top"></a>
### Отчёт типа `top`

Пример фрагмента отчёта типа `top` для одного дня:

![NGINX Unit log analyzer. Report type `top`](./assets/nginx-unit-log-analyzer-report-type-top.png)

В таблице отчёта:

* `DATE` — анализируемая дата с указанием часового пояса;
* `APP` — список имён приложений, которые были запущены за анализируемую дату;
* `Duration` — продолжительность исполнения процесса;
* `App name` — имя приложения;
* `Start time` — дата и время запуска процесса;
* `Exit time` — дата и время завершения процесса;
* `Start` — номер строки в лог-файле NGINX Unit, в которой был зафиксирован запуск процесса;
* `End` — номер строки в лог-файле NGINX Unit, в которой было зафиксировано завершение процесса;
* `Id` — [идентификатор процесса](https://en.wikipedia.org/wiki/Process_identifier) в NGINХ Unit.

В верхней части таблицы содержится список из 20 наиболее продолжительных процессов,
которые были зафиксированы за анализируемую дату.

В нижней части таблицы содержится список из 5 наименее продолжительных процессов,
которые были зафиксированы за анализируемую дату.

Списки отсортированы по убыванию продолжительности.

<a id="%D0%9E%D1%82%D1%87%D1%91%D1%82-%D1%82%D0%B8%D0%BF%D0%B0-graph"></a>
### Отчёт типа `graph`

Пример отчёта типа `graph`, включающий 2 графика:

* график количества процессов NGINX Unit для запущенных приложений с усреднением за один час;
* график [медианной](https://en.wikipedia.org/wiki/Median) продолжительности исполнения процессов.

![NGINX Unit log analyzer. Report type `graph`](./assets/nginx-unit-log-analyzer-logo.png)

Формат значений продолжительности исполнения процессов NGINX Unit на графике имеет вид:
`H:MM:SS`, где: `H` — часы, `MM` — минуты, `SS` — секунды.

<a id="%D0%90%D0%B2%D1%82%D0%BE%D1%80%D1%8B"></a>
## Авторы

© 2024-2025 andrey-tech

<a id="%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F"></a>
## Лицензия

Данная библиотека распространяется на условиях лицензии [MIT](./LICENSE).
