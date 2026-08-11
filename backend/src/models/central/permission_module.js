const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('permission_module', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    user_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    module: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    menu: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    controller: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    action: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false
    },
    featured: {
      type: DataTypes.ENUM('0','1'),
      allowNull: false,
      defaultValue: "0"
    },
    sort_no: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    edit: {
      type: DataTypes.ENUM('0','1'),
      allowNull: true,
      defaultValue: "0"
    },
    delete: {
      type: DataTypes.ENUM('0','1'),
      allowNull: true,
      defaultValue: "0"
    },
    featured_sort: {
      type: DataTypes.INTEGER,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'permission_module',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
