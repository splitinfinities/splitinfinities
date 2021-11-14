import { Component } from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
})
export class AppComponent {
  title = 'Split Infinities';
  name = 'Home';
  description = 'Ramblings about design, code, comics, and movies.';
  image = 'https://www.splitinfinities.com/assets/img/will/medium.jpg';
  keywords = [''].join(',');
  site_url = 'https://angular.splitinfinities.com';
}
